<?php

namespace PHP_Parallel_Lint\PhpConsoleHighlighter\Test;

use PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor;
use PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter;

/**
 * @coversDefaultClass PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter
 */
class GetWholeFileTest extends HighlighterTestCase
{
    /** @var Highlighter */
    private $highlighter;

    /** @var string */
    private static $input = <<<'EOL'
<?php

namespace FooBar;

class Foo {
    /**
     * Docblock.
     *
     * @param type $param Description.
     */
    public function bar($param) {
        // Do something.
    }
}
?>
EOL;

    /**
     * Set up the class under test.
     *
     * @before
     */
    protected function setUpHighlighter()
    {
        $this->highlighter = new Highlighter($this->getConsoleColorMock());
    }

    /**
     * Test retrieving the highlighted contents of a complete file.
     *
     * @covers ::getWholeFile
     * @covers ::getHighlightedLines
     * @covers ::splitToLines
     *
     * @dataProvider dataGetWholeFile
     *
     * @param string $input    The input string.
     * @param string $expected Expected function output.
     */
    public function testGetWholeFile($input, $expected)
    {
        $output = $this->highlighter->getWholeFile($input);
        // Allow unit tests to succeed on non-*nix systems.
        $output = str_replace(array("\r\n", "\r"), "\n", $output);

        $this->assertSame($expected, $output);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function dataGetWholeFile()
    {
        return array(
            'Empty source' => array(
                'input'    => '',
                'expected' => '',
            ),
            'Single line' => array(
                'input'    => <<<'EOL'
text <?= $var ?> more text
EOL
                ,
                'expected' => <<<'EOL'
<token_html>text </token_html><token_default><?= $var ?></token_default><token_html> more text</token_html>
EOL
            ),
            'Multi-line' => array(
                'input'    => self::$input,
                'expected' => <<<'EOL'
<token_default><?php</token_default>

<token_keyword>namespace </token_keyword><token_default>FooBar</token_default><token_keyword>;</token_keyword>

<token_keyword>class </token_keyword><token_default>Foo </token_default><token_keyword>{</token_keyword>
<token_keyword>    </token_keyword><token_comment>/**</token_comment>
<token_comment>     * Docblock.</token_comment>
<token_comment>     *</token_comment>
<token_comment>     * @param type $param Description.</token_comment>
<token_comment>     */</token_comment>
<token_comment>    </token_comment><token_keyword>public function </token_keyword><token_default>bar</token_default><token_keyword>(</token_keyword><token_default>$param</token_default><token_keyword>) {</token_keyword>
<token_keyword>        </token_keyword><token_comment>// Do something.</token_comment>
<token_comment>    </token_comment><token_keyword>}</token_keyword>
<token_keyword>}</token_keyword>
<token_default>?></token_default>
EOL
            ),
        );
    }

    /**
     * Test retrieving the highlighted contents of a complete file with line numbers.
     *
     * @covers ::getWholeFileWithLineNumbers
     * @covers ::getHighlightedLines
     * @covers ::splitToLines
     * @covers ::lineNumbers
     */
    public function testGetWholeFileWithLineNumbers()
    {
        $expected = <<<'EOL'
<line_number> 1| </line_number><token_default><?php</token_default>
<line_number> 2| </line_number>
<line_number> 3| </line_number><token_keyword>namespace </token_keyword><token_default>FooBar</token_default><token_keyword>;</token_keyword>
<line_number> 4| </line_number>
<line_number> 5| </line_number><token_keyword>class </token_keyword><token_default>Foo </token_default><token_keyword>{</token_keyword>
<line_number> 6| </line_number><token_keyword>    </token_keyword><token_comment>/**</token_comment>
<line_number> 7| </line_number><token_comment>     * Docblock.</token_comment>
<line_number> 8| </line_number><token_comment>     *</token_comment>
<line_number> 9| </line_number><token_comment>     * @param type $param Description.</token_comment>
<line_number>10| </line_number><token_comment>     */</token_comment>
<line_number>11| </line_number><token_comment>    </token_comment><token_keyword>public function </token_keyword><token_default>bar</token_default><token_keyword>(</token_keyword><token_default>$param</token_default><token_keyword>) {</token_keyword>
<line_number>12| </line_number><token_keyword>        </token_keyword><token_comment>// Do something.</token_comment>
<line_number>13| </line_number><token_comment>    </token_comment><token_keyword>}</token_keyword>
<line_number>14| </line_number><token_keyword>}</token_keyword>
<line_number>15| </line_number><token_default>?></token_default>
EOL;

        $output = $this->highlighter->getWholeFileWithLineNumbers(self::$input);
        // Allow unit tests to succeed on non-*nix systems.
        $output = str_replace(array("\r\n", "\r"), "\n", $output);

        $this->assertSame($expected, $output);
    }
}
