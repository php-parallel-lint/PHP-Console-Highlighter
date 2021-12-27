<?php

namespace PHP_Parallel_Lint\PhpConsoleHighlighter\Test;

use PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter;

/**
 * @covers PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter::getCodeSnippet
 * @covers PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter::getHighlightedLines
 * @covers PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter::splitToLines
 * @covers PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter::lineNumbers
 */
class GetCodeSnippetTest extends HighlighterTestCase
{
    /** @var string */
    private $input = <<<'EOL'
<?php

namespace FooBar;

class Foo {
    /**
     * @param type $param Description.
     */
    public function bar($param) {
        // Do something.
    }
}
?>
EOL;

    /**
     * Test retrieving a code snippet from a larger context.
     *
     * @dataProvider dataGetCodeSnippet
     *
     * @param string $expected         Expected function output.
     * @param int    $lineNo           Line number to get the code snippet for.
     * @param int    $linesBeforeAfter Number of lines of code context to retrieve.
     */
    public function testGetCodeSnippet($expected, $lineNo, $linesBeforeAfter = 2)
    {
        $highlighter = new Highlighter($this->getConsoleColorMock());
        $output      = $highlighter->getCodeSnippet($this->input, $lineNo, $linesBeforeAfter, $linesBeforeAfter);

        // Allow unit tests to succeed on non-*nix systems.
        $output = str_replace(array("\r\n", "\r"), "\n", $output);

        $this->assertSame($expected, $output);
    }

    /**
     * Data provider.
     *
     * Includes test cases to verify that the line number padding is handled correctly
     * depending on the "widest" line number.
     *
     * @return array
     */
    public function dataGetCodeSnippet()
    {
        return array(
            'Snippet at start of code - line 1' => array(
                'expected' => <<<'EOL'
<actual_line_mark>  > </actual_line_mark><line_number>1| </line_number><token_default><?php</token_default>
    <line_number>2| </line_number>
    <line_number>3| </line_number><token_keyword>namespace </token_keyword><token_default>FooBar</token_default><token_keyword>;</token_keyword>
EOL
                ,
                'lineNo'   => 1,
            ),
            'Snippet at start of code - line 2' => array(
                'expected' => <<<'EOL'
    <line_number>1| </line_number><token_default><?php</token_default>
<actual_line_mark>  > </actual_line_mark><line_number>2| </line_number>
    <line_number>3| </line_number><token_keyword>namespace </token_keyword><token_default>FooBar</token_default><token_keyword>;</token_keyword>
    <line_number>4| </line_number>
EOL
                ,
                'lineNo'   => 2,
            ),
            'Snippet middle of code' => array(
                'expected' => <<<'EOL'
    <line_number> 7| </line_number><token_comment>     * @param type $param Description.</token_comment>
    <line_number> 8| </line_number><token_comment>     */</token_comment>
<actual_line_mark>  > </actual_line_mark><line_number> 9| </line_number><token_comment>    </token_comment><token_keyword>public function </token_keyword><token_default>bar</token_default><token_keyword>(</token_keyword><token_default>$param</token_default><token_keyword>) {</token_keyword>
    <line_number>10| </line_number><token_keyword>        </token_keyword><token_comment>// Do something.</token_comment>
    <line_number>11| </line_number><token_comment>    </token_comment><token_keyword>}</token_keyword>
EOL
                ,
                'lineNo'   => 9,
            ),
            'Snippet at end of code - line before last' => array(
                'expected' => <<<'EOL'
    <line_number>10| </line_number><token_keyword>        </token_keyword><token_comment>// Do something.</token_comment>
    <line_number>11| </line_number><token_comment>    </token_comment><token_keyword>}</token_keyword>
<actual_line_mark>  > </actual_line_mark><line_number>12| </line_number><token_keyword>}</token_keyword>
    <line_number>13| </line_number><token_default>?></token_default>
EOL
                ,
                'lineNo'   => 12,
            ),
            'Snippet at end of code - last line' => array(
                'expected' => <<<'EOL'
    <line_number>11| </line_number><token_comment>    </token_comment><token_keyword>}</token_keyword>
    <line_number>12| </line_number><token_keyword>}</token_keyword>
<actual_line_mark>  > </actual_line_mark><line_number>13| </line_number><token_default>?></token_default>
EOL
                ,
                'lineNo'   => 13,
            ),
            'Snippet middle of code, 1 line context' => array(
                'expected'         => <<<'EOL'
    <line_number> 8| </line_number><token_comment>     */</token_comment>
<actual_line_mark>  > </actual_line_mark><line_number> 9| </line_number><token_comment>    </token_comment><token_keyword>public function </token_keyword><token_default>bar</token_default><token_keyword>(</token_keyword><token_default>$param</token_default><token_keyword>) {</token_keyword>
    <line_number>10| </line_number><token_keyword>        </token_keyword><token_comment>// Do something.</token_comment>
EOL
                ,
                'lineNo'           => 9,
                'linesBeforeAfter' => 1,
            ),
            'Snippet middle of code, 3 line context' => array(
                'expected'         => <<<'EOL'
    <line_number> 6| </line_number><token_keyword>    </token_keyword><token_comment>/**</token_comment>
    <line_number> 7| </line_number><token_comment>     * @param type $param Description.</token_comment>
    <line_number> 8| </line_number><token_comment>     */</token_comment>
<actual_line_mark>  > </actual_line_mark><line_number> 9| </line_number><token_comment>    </token_comment><token_keyword>public function </token_keyword><token_default>bar</token_default><token_keyword>(</token_keyword><token_default>$param</token_default><token_keyword>) {</token_keyword>
    <line_number>10| </line_number><token_keyword>        </token_keyword><token_comment>// Do something.</token_comment>
    <line_number>11| </line_number><token_comment>    </token_comment><token_keyword>}</token_keyword>
    <line_number>12| </line_number><token_keyword>}</token_keyword>
EOL
                ,
                'lineNo'           => 9,
                'linesBeforeAfter' => 3,
            ),
        );
    }
}
