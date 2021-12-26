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

    protected function compare($original, $expected)
    {
        $output = $this->uut->getWholeFile($original);
        // Allow unit tests to succeed on non-*nix systems.
        $output = str_replace(array("\r\n", "\r"), "\n", $output);

        $this->assertSame($expected, $output);
    }

    public function testVariable()
    {
        $this->compare(
            <<<EOL
<?php
echo \$a;
EOL
            ,
            <<<EOL
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>\$a</token_default><token_keyword>;</token_keyword>
EOL
        );
    }

    public function testInteger()
    {
        $this->compare(
            <<<EOL
<?php
echo 43;
EOL
            ,
            <<<EOL
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>43</token_default><token_keyword>;</token_keyword>
EOL
        );
    }

    public function testFloat()
    {
        $this->compare(
            <<<EOL
<?php
echo 43.3;
EOL
            ,
            <<<EOL
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>43.3</token_default><token_keyword>;</token_keyword>
EOL
        );
    }

    public function testHex()
    {
        $this->compare(
            <<<EOL
<?php
echo 0x43;
EOL
            ,
            <<<EOL
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_default>0x43</token_default><token_keyword>;</token_keyword>
EOL
        );
    }

    public function testBasicFunction()
    {
        $this->compare(
            <<<EOL
<?php
function plus(\$a, \$b) {
    return \$a + \$b;
}
EOL
            ,
            <<<EOL
<token_default><?php</token_default>
<token_keyword>function </token_keyword><token_default>plus</token_default><token_keyword>(</token_keyword><token_default>\$a</token_default><token_keyword>, </token_keyword><token_default>\$b</token_default><token_keyword>) {</token_keyword>
<token_keyword>    return </token_keyword><token_default>\$a </token_default><token_keyword>+ </token_keyword><token_default>\$b</token_default><token_keyword>;</token_keyword>
<token_keyword>}</token_keyword>
EOL
        );
    }

    public function testStringNormal()
    {
        $this->compare(
            <<<EOL
<?php
echo 'Ahoj světe';
EOL
            ,
            <<<EOL
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_string>'Ahoj světe'</token_string><token_keyword>;</token_keyword>
EOL
        );
    }

    public function testStringDouble()
    {
        $this->compare(
            <<<EOL
<?php
echo "Ahoj světe";
EOL
            ,
            <<<EOL
<token_default><?php</token_default>
<token_keyword>echo </token_keyword><token_string>"Ahoj světe"</token_string><token_keyword>;</token_keyword>
EOL
        );
    }

    public function testInstanceof()
    {
        $this->compare(
            <<<EOL
<?php
\$a instanceof stdClass;
EOL
            ,
            <<<EOL
<token_default><?php</token_default>
<token_default>\$a </token_default><token_keyword>instanceof </token_keyword><token_default>stdClass</token_default><token_keyword>;</token_keyword>
EOL
        );
    }

    /*
     * Constants
     */
    public function testConstant()
    {
        $constants = array(
            '__FILE__',
            '__LINE__',
            '__CLASS__',
            '__FUNCTION__',
            '__METHOD__',
            '__TRAIT__',
            '__DIR__',
            '__NAMESPACE__'
        );

        foreach ($constants as $constant) {
            $this->compare(
                <<<EOL
<?php
$constant;
EOL
                ,
                <<<EOL
<token_default><?php</token_default>
<token_default>$constant</token_default><token_keyword>;</token_keyword>
EOL
            );
        }
    }

    /*
     * Comments
     */
    public function testComment()
    {
        $this->compare(
            <<<EOL
<?php
/* Ahoj */
EOL
            ,
            <<<EOL
<token_default><?php</token_default>
<token_comment>/* Ahoj */</token_comment>
EOL
        );
    }

    public function testDocComment()
    {
        $this->compare(
            <<<EOL
<?php
/** Ahoj */
EOL
            ,
            <<<EOL
<token_default><?php</token_default>
<token_comment>/** Ahoj */</token_comment>
EOL
        );
    }

    public function testInlineComment()
    {
        $this->compare(
            <<<EOL
<?php
// Ahoj
EOL
            ,
            <<<EOL
<token_default><?php</token_default>
<token_comment>// Ahoj</token_comment>
EOL
        );
    }

    public function testHashComment()
    {
        $this->compare(
            <<<EOL
<?php
# Ahoj
EOL
            ,
            <<<EOL
<token_default><?php</token_default>
<token_comment># Ahoj</token_comment>
EOL
        );
    }

    public function testEmpty()
    {
        $this->compare(
            '',
            ''
        );
    }

    public function testWhitespace()
    {
        $this->compare(
            ' ',
            '<token_html> </token_html>'
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
