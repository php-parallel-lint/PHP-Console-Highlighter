<?php

namespace PHP_Parallel_Lint\PhpConsoleHighlighter\Test;

use PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter;
use PHPUnit\Framework\TestCase;

/**
 * Test support for all token types.
 *
 * @covers PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter::tokenize
 * @covers PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter::getTokenType
 */
class TokenizeTest extends TestCase
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
     * Helper method mocking the Console Color Class.
     *
     * @return \PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor
     */
    protected function getConsoleColorMock()
    {
        $mock = method_exists($this, 'createMock')
            ? $this->createMock('\PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor')
            : $this->getMock('\PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor');

        $mock->expects($this->any())
            ->method('apply')
            ->will($this->returnCallback(function ($style, $text) {
                return "<$style>$text</$style>";
            }));

        $mock->expects($this->any())
            ->method('hasTheme')
            ->will($this->returnValue(true));

        return $mock;
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
    public function dataEmptyFiles()
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
    public function dataPhpTags()
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
    public function dataMagicConstants()
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
    public function dataMiscTokens()
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
    public function dataComments()
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

    public function testBasicFunction()
    {
        $this->compare(
            <<<'EOL'
<?php
function plus($a, $b) {
    return $a + $b;
}
EOL
            ,
            <<<'EOL'
<token_default><?php</token_default>
<token_keyword>function </token_keyword><token_default>plus</token_default><token_keyword>(</token_keyword><token_default>$a</token_default><token_keyword>, </token_keyword><token_default>$b</token_default><token_keyword>) {</token_keyword>
<token_keyword>    return </token_keyword><token_default>$a </token_default><token_keyword>+ </token_keyword><token_default>$b</token_default><token_keyword>;</token_keyword>
<token_keyword>}</token_keyword>
EOL
        );
    }

    public function testStringNormal()
    {
        $this->compare(
            <<<'EOL'
<?php
echo 'Ahoj světe';
EOL
            ,
            <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_string>'Ahoj světe'</token_string><token_keyword>;</token_keyword>
EOL
        );
    }

    public function testStringDouble()
    {
        $this->compare(
            <<<'EOL'
<?php
echo "Ahoj světe";
EOL
            ,
            <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_string>"Ahoj světe"</token_string><token_keyword>;</token_keyword>
EOL
        );
    }

    public function testInstanceof()
    {
        $this->compare(
            <<<'EOL'
<?php
$a instanceof stdClass;
EOL
            ,
            <<<'EOL'
<token_default><?php</token_default>
<token_default>$a </token_default><token_keyword>instanceof </token_keyword><token_default>stdClass</token_default><token_keyword>;</token_keyword>
EOL
        );
    }

    public function testFunctionCall()
    {
        $this->compare(
            <<<'EOL'
<?php
echo functionName();
EOL
            ,
            <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>functionName</token_default><token_keyword>();</token_keyword>
EOL
        );
    }

    public function testFQNFunctionCall()
    {
        $original = <<<'EOL'
<?php
echo \My\Package\functionName();
EOL;

        if (PHP_VERSION_ID < 80000) {
            $expected = <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo \</token_keyword><token_default>My</token_default><token_keyword>\</token_keyword><token_default>Package</token_default><token_keyword>\</token_keyword><token_default>functionName</token_default><token_keyword>();</token_keyword>
EOL;
        } else {
            $expected = <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>\My\Package\functionName</token_default><token_keyword>();</token_keyword>
EOL;
        }

        $this->compare($original, $expected);
    }

    public function testNamespaceRelativeFunctionCall()
    {
        $original = <<<'EOL'
<?php
echo namespace\functionName();
EOL;

        if (PHP_VERSION_ID < 80000) {
            $expected = <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo namespace\</token_keyword><token_default>functionName</token_default><token_keyword>();</token_keyword>
EOL;
        } else {
            $expected = <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>namespace\functionName</token_default><token_keyword>();</token_keyword>
EOL;
        }

        $this->compare($original, $expected);
    }

    public function testQualifiedFunctionCall()
    {
        $original = <<<'EOL'
<?php
echo Package\functionName();
EOL;

        if (PHP_VERSION_ID < 80000) {
            $expected = <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>Package</token_default><token_keyword>\</token_keyword><token_default>functionName</token_default><token_keyword>();</token_keyword>
EOL;
        } else {
            $expected = <<<'EOL'
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>Package\functionName</token_default><token_keyword>();</token_keyword>
EOL;
        }

        $this->compare($original, $expected);
    }
}
