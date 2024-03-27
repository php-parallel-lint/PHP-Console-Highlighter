<?php

namespace PHP_Parallel_Lint\PhpConsoleHighlighter\Test;

use PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter;

/**
 * Test support for all token types.
 *
 * @covers PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter::tokenize
 * @covers PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter::getTokenType
 */
class TokenizeTest extends HighlighterTestCase
{
    /** @var Highlighter */
    private $uut;

    /**
     * @before
     */
    protected function setUpHighlighter()
    {
        $this->uut = new Highlighter($this->getConsoleColorMock());
    }

    /**
     * Helper method executing the actual tests.
     *
     * {@internal This is a work-around to allow for supporting PHPUnit 4.x.
     * In PHPUnit 5 and higher, a test method can have multiple data provider tags and
     * will execute the test cases for each. Unfortunately, that wasn't supported in PHPUnit 4.x.
     * This effectively means that each data provider needs its own test method for now.}
     *
     * @param string $original The input string.
     * @param string $expected The expected output string.
     */
    protected function compare($original, $expected)
    {
        $output = $this->uut->getWholeFile($original);
        // Allow unit tests to succeed on non-*nix systems.
        $output = str_replace(array("\r\n", "\r"), "\n", $output);

        $this->assertSame($expected, $output);
    }

    /**
     * Test the tokenizer and token specific highlighting of "empty" inputs.
     *
     * @dataProvider dataEmptyFiles
     *
     * @param string $original The input string.
     * @param string $expected The expected output string.
     */
    public function testEmptyFiles($original, $expected)
    {
        $this->compare($original, $expected);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function dataEmptyFiles()
    {
        return array(
            'Empty file' => array(
                'original' => '',
                'expected' => '',
            ),
            'File only containing whitespace' => array(
                'original' => '  ',
                'expected' => '<token_html>  </token_html>',
            ),
        );
    }

    /**
     * Test the tokenizer and token specific highlighting of PHP tag tokens.
     *
     * @dataProvider dataPhpTags
     *
     * @param string $original The input string.
     * @param string $expected The expected output string.
     */
    public function testPhpTags($original, $expected)
    {
        $this->compare($original, $expected);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function dataPhpTags()
    {
        return array(
            '"Long" open tag with close tag' => array(
                'original' => <<<'EOL'
<?php echo PHP_EOL; ?>
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php </token_default><token_keyword>echo </token_keyword><token_default>PHP_EOL</token_default><token_keyword>; </token_keyword><token_default>?></token_default>
EOL
            ),
            'Short open tag with close tag' => array(
                'original' => <<<'EOL'
text <?= /* comment */ ?> more text
EOL
                ,
                'expected' => <<<'EOL'
<token_html>text </token_html><token_default><?= </token_default><token_comment>/* comment */ </token_comment><token_default>?></token_default><token_html> more text</token_html>
EOL
            ),
        );
    }

    /**
     * Test the tokenizer and token specific highlighting of the magic constants.
     *
     * @dataProvider dataMagicConstants
     *
     * @param string $original The input string.
     * @param string $expected The expected output string.
     */
    public function testMagicConstants($original, $expected)
    {
        $this->compare($original, $expected);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function dataMagicConstants()
    {
        $magicConstants = array(
            '__FILE__',
            '__LINE__',
            '__CLASS__',
            '__FUNCTION__',
            '__METHOD__',
            '__TRAIT__',
            '__DIR__',
            '__NAMESPACE__'
        );

        $data = array();
        foreach ($magicConstants as $constant) {
            $data['Magic constant: ' . $constant] = array(
                'original' => <<<EOL
<?php
$constant;
EOL
                ,
                'expected' => <<<EOL
<token_default><?php</token_default>
<token_default>$constant</token_default><token_keyword>;</token_keyword>
EOL
            );
        }

        return $data;
    }

    /**
     * Test the tokenizer and token specific highlighting of the "miscellaneous" tokens.
     *
     * @dataProvider dataMiscTokens
     *
     * @param string $original The input string.
     * @param string $expected The expected output string.
     */
    public function testMiscTokens($original, $expected)
    {
        $this->compare($original, $expected);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function dataMiscTokens()
    {
        return array(
            'Constant (T_STRING)' => array(
                'original' => <<<'EOL'
<?php
echo PHP_EOL;
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>PHP_EOL</token_default><token_keyword>;</token_keyword>
EOL
            ),
            'Variable' => array(
                'original' => <<<'EOL'
<?php
echo $a;
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>$a</token_default><token_keyword>;</token_keyword>
EOL
            ),
            'Integer: decimal' => array(
                'original' => <<<'EOL'
<?php
echo 43;
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>43</token_default><token_keyword>;</token_keyword>
EOL
            ),
            'Integer: hexadecimal' => array(
                'original' => <<<'EOL'
<?php
echo 0x43;
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>0x43</token_default><token_keyword>;</token_keyword>
EOL
            ),
            'Float' => array(
                'original' => <<<'EOL'
<?php
echo 43.3;
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>43.3</token_default><token_keyword>;</token_keyword>
EOL
            ),
        );
    }

    /**
     * Test the tokenizer and token specific highlighting of comment tokens.
     *
     * @dataProvider dataComments
     *
     * @param string $original The input string.
     * @param string $expected The expected output string.
     */
    public function testComments($original, $expected)
    {
        $this->compare($original, $expected);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function dataComments()
    {
        return array(
            'Doc block: single line' => array(
                'original' => <<<'EOL'
<?php
/** Ahoj */
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_comment>/** Ahoj */</token_comment>
EOL
            ),
            'Doc block: multi line' => array(
                'original' => <<<'EOL'
<?php
/**
 * Ahoj
 *
 * @param type $name Description
 */
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_comment>/**</token_comment>
<token_comment> * Ahoj</token_comment>
<token_comment> *</token_comment>
<token_comment> * @param type $name Description</token_comment>
<token_comment> */</token_comment>
EOL
            ),
            'Star comment: single line' => array(
                'original' => <<<'EOL'
<?php
/* Ahoj */
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_comment>/* Ahoj */</token_comment>
EOL
            ),
            'Star comment: multi line' => array(
                'original' => <<<'EOL'
<?php
/*
    Ahoj
 */
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_comment>/*</token_comment>
<token_comment>    Ahoj</token_comment>
<token_comment> */</token_comment>
EOL
            ),
            'Slash comment' => array(
                'original' => <<<'EOL'
<?php
// Ahoj
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_comment>// Ahoj</token_comment>
EOL
            ),
            'Slash comment, multiple' => array(
                'original' => <<<'EOL'
<?php
// Ahoj
// Ahoj again
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_comment>// Ahoj</token_comment>
<token_comment>// Ahoj again</token_comment>
EOL
            ),
            'Hash comment' => array(
                'original' => <<<'EOL'
<?php
# Ahoj
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_comment># Ahoj</token_comment>
EOL
            ),
        );
    }

    /**
     * Test the tokenizer and token specific highlighting of text string tokens.
     *
     * @dataProvider dataTextStrings
     *
     * @param string $original The input string.
     * @param string $expected The expected output string.
     */
    public function testTextStrings($original, $expected)
    {
        $this->compare($original, $expected);
    }

    /**
     * Data provider for testing text string tokens.
     *
     * @return array
     */
    public static function dataTextStrings()
    {
        return array(
            'Single quoted text string' => array(
                'original' => <<<'EOL'
<?php
echo 'Ahoj světe';
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_string>'Ahoj světe'</token_string><token_keyword>;</token_keyword>
EOL
            ),
            'Double quoted text string' => array(
                'original' => <<<'EOL'
<?php
echo "Ahoj světe";
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_string>"Ahoj světe"</token_string><token_keyword>;</token_keyword>
EOL
            ),
            'Double quoted text string with interpolation [1]' => array(
                'original' => <<<'EOL'
<?php
echo "Ahoj $text and more text";
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_string>"Ahoj </token_string><token_default>$text</token_default><token_string> and more text"</token_string><token_keyword>;</token_keyword>
EOL
            ),
            'Double quoted text string with interpolation [2]' => array(
                'original' => <<<'EOL'
<?php
echo "$text and more text";
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_string>"</token_string><token_default>$text</token_default><token_string> and more text"</token_string><token_keyword>;</token_keyword>
EOL
            ),
            'Double quoted text string with interpolation [3]' => array(
                'original' => <<<'EOL'
<?php
echo "Ahoj {$obj->prop} and more text";
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_string>"Ahoj </token_string><token_keyword>{</token_keyword><token_default>$obj</token_default><token_keyword>-></token_keyword><token_default>prop</token_default><token_keyword>}</token_keyword><token_string> and more text"</token_string><token_keyword>;</token_keyword>
EOL
            ),
            'Nowdoc' => array(
                'original' => '<?php
echo <<<\'TXT\'
Text
TXT;
',
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo <<<'TXT'</token_keyword>
<token_string>Text</token_string>
<token_keyword>TXT;</token_keyword>

EOL
            ),
            'Heredoc' => array(
                'original' => '<?php
echo <<<TXT
Text
TXT;
',
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo <<<TXT</token_keyword>
<token_string>Text</token_string>
<token_keyword>TXT;</token_keyword>

EOL
            ),
            'Heredoc with interpolation' => array(
                'original' => '<?php
echo <<<TXT
Text $text
TXT;
',
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo <<<TXT</token_keyword>
<token_string>Text </token_string><token_default>$text</token_default>
<token_keyword>TXT;</token_keyword>

EOL
            ),
        );
    }

    /**
     * Test the tokenizer and token specific highlighting of inline HTML tokens.
     *
     * @dataProvider dataInlineHtml
     *
     * @param string $original The input string.
     * @param string $expected The expected output string.
     */
    public function testInlineHtml($original, $expected)
    {
        $this->compare($original, $expected);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function dataInlineHtml()
    {
        return array(
            'Inline HTML' => array(
                'original' => <<<'EOL'
<div><?= $text ?></div>
EOL
                ,
                'expected' => <<<'EOL'
<token_html><div></token_html><token_default><?= $text ?></token_default><token_html></div></token_html>
EOL
            ),
        );
    }

    /**
     * Test the tokenizer and token specific highlighting of name tokens.
     *
     * @dataProvider dataNameTokens
     *
     * @param string $original The input string.
     * @param string $expected The expected output string.
     */
    public function testNameTokens($original, $expected)
    {
        $this->compare($original, $expected);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function dataNameTokens()
    {
        $data = array(
            'Unqualified function call' => array(
                'original' => <<<'EOL'
<?php
echo functionName();
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>functionName</token_default><token_keyword>();</token_keyword>
EOL
            ),
        );

        $data['Fully qualified function call'] = array(
            'original' => <<<'EOL'
<?php
echo \My\Package\functionName();
EOL
        );
        if (PHP_VERSION_ID < 80000) {
            $data['Fully qualified function call']['expected'] = <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo \</token_keyword><token_default>My</token_default><token_keyword>\</token_keyword><token_default>Package</token_default><token_keyword>\</token_keyword><token_default>functionName</token_default><token_keyword>();</token_keyword>
EOL;
        } else {
            $data['Fully qualified function call']['expected'] = <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>\My\Package\functionName</token_default><token_keyword>();</token_keyword>
EOL;
        }

        $data['Namespace relative function call'] = array(
            'original' => <<<'EOL'
<?php
echo namespace\functionName();
EOL
        );
        if (PHP_VERSION_ID < 80000) {
            $data['Namespace relative function call']['expected'] = <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo namespace\</token_keyword><token_default>functionName</token_default><token_keyword>();</token_keyword>
EOL;
        } else {
            $data['Namespace relative function call']['expected'] = <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>namespace\functionName</token_default><token_keyword>();</token_keyword>
EOL;
        }

        $data['Partially qualified function call'] = array(
            'original' => <<<'EOL'
<?php
echo Package\functionName();
EOL
        );
        if (PHP_VERSION_ID < 80000) {
            $data['Partially qualified function call']['expected'] = <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>Package</token_default><token_keyword>\</token_keyword><token_default>functionName</token_default><token_keyword>();</token_keyword>
EOL;
        } else {
            $data['Partially qualified function call']['expected'] = <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>Package\functionName</token_default><token_keyword>();</token_keyword>
EOL;
        }

        return $data;
    }

    /**
     * Test the tokenizer and token specific highlighting of keyword and operator tokens.
     *
     * @dataProvider dataKeywordsAndOperators
     *
     * @param string $original The input string.
     * @param string $expected The expected output string.
     */
    public function testKeywordsAndOperators($original, $expected)
    {
        $this->compare($original, $expected);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function dataKeywordsAndOperators()
    {
        return array(
            'Keywords: instanceof' => array(
                'original' => <<<'EOL'
<?php
$a instanceof stdClass;
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_default>$a </token_default><token_keyword>instanceof </token_keyword><token_default>stdClass</token_default><token_keyword>;</token_keyword>
EOL
            ),
            'Keywords: function, return' => array(
                'original' => <<<'EOL'
<?php
function plus($a, $b) {
    return $a + $b;
}
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_keyword>function </token_keyword><token_default>plus</token_default><token_keyword>(</token_keyword><token_default>$a</token_default><token_keyword>, </token_keyword><token_default>$b</token_default><token_keyword>) {</token_keyword>
<token_keyword>    return </token_keyword><token_default>$a </token_default><token_keyword>+ </token_keyword><token_default>$b</token_default><token_keyword>;</token_keyword>
<token_keyword>}</token_keyword>
EOL
            ),
            'Keywords: while, empty, exit' => array(
                'original' => <<<'EOL'
<?php
while(empty($a)) { exit; }
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_keyword>while(empty(</token_keyword><token_default>$a</token_default><token_keyword>)) { exit; }</token_keyword>
EOL
            ),
            'Keywords: type casts' => array(
                'original' => <<<'EOL'
<?php
$a = (int) (bool) $a . (string) $b;
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_default>$a </token_default><token_keyword>= (int) (bool) </token_keyword><token_default>$a </token_default><token_keyword>. (string) </token_keyword><token_default>$b</token_default><token_keyword>;</token_keyword>
EOL
            ),
            'Keywords: new, clone' => array(
                'original' => <<<'EOL'
<?php
$obj = new stdClass;
$clone = clone $obj;
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_default>$obj </token_default><token_keyword>= new </token_keyword><token_default>stdClass</token_default><token_keyword>;</token_keyword>
<token_default>$clone </token_default><token_keyword>= clone </token_keyword><token_default>$obj</token_default><token_keyword>;</token_keyword>
EOL
            ),
            'Operators: arithmetic operators' => array(
                'original' => <<<'EOL'
<?php
echo 1 + 2 - 2 * 10 / 5 ** 1;
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>1 </token_default><token_keyword>+ </token_keyword><token_default>2 </token_default><token_keyword>- </token_keyword><token_default>2 </token_default><token_keyword>* </token_keyword><token_default>10 </token_default><token_keyword>/ </token_keyword><token_default>5 </token_default><token_keyword>** </token_keyword><token_default>1</token_default><token_keyword>;</token_keyword>
EOL
            ),
            'Operators: assignment operators' => array(
                'original' => <<<'EOL'
<?php
$a = 10;
$a *= 10;
$a ^= 10;
$a ??= $b;
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_default>$a </token_default><token_keyword>= </token_keyword><token_default>10</token_default><token_keyword>;</token_keyword>
<token_default>$a </token_default><token_keyword>*= </token_keyword><token_default>10</token_default><token_keyword>;</token_keyword>
<token_default>$a </token_default><token_keyword>^= </token_keyword><token_default>10</token_default><token_keyword>;</token_keyword>
<token_default>$a </token_default><token_keyword>??= </token_keyword><token_default>$b</token_default><token_keyword>;</token_keyword>
EOL
            ),
            'Operators: comparison, boolean and logical operators' => array(
                'original' => <<<'EOL'
<?php
echo '' === '' && '' !== '';
echo true || '' > '';
echo '' <=> '' and '' or ! '';
EOL
                ,
                'expected' => <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_string>'' </token_string><token_keyword>=== </token_keyword><token_string>'' </token_string><token_keyword>&& </token_keyword><token_string>'' </token_string><token_keyword>!== </token_keyword><token_string>''</token_string><token_keyword>;</token_keyword>
<token_keyword>echo </token_keyword><token_default>true </token_default><token_keyword>|| </token_keyword><token_string>'' </token_string><token_keyword>> </token_keyword><token_string>''</token_string><token_keyword>;</token_keyword>
<token_keyword>echo </token_keyword><token_string>'' </token_string><token_keyword><=> </token_keyword><token_string>'' </token_string><token_keyword>and </token_keyword><token_string>'' </token_string><token_keyword>or ! </token_keyword><token_string>''</token_string><token_keyword>;</token_keyword>
EOL
            ),
        );
    }
}
