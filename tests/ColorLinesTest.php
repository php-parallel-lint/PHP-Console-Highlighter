<?php

namespace PHP_Parallel_Lint\PhpConsoleHighlighter\Test;

use PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor;
use PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter;
use ReflectionMethod;

/**
 * @covers PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter::colorLines
 */
class ColorLinesTest extends HighlighterTestCase
{
    /** @var string */
    private static $input = array(
        7 => array (
            0 => array (
                0 => 'token_keyword',
                1 => 'function ',
            ),
            1 => array (
                0 => 'token_default',
                1 => 'bar',
            ),
            2 => array (
                0 => 'token_keyword',
                1 => '(',
            ),
            3 => array (
                0 => 'token_default',
                1 => '$param',
            ),
            4 => array (
                0 => 'token_unknown',
                1 => ') ',
            ),
            5 => array (
                0 => 'token_keyword',
                1 => '{}',
            ),
        ),
    );

    /**
     * Test the (private) `colorlines()` method.
     *
     * @dataProvider dataColorLines
     *
     * @param array $input     Tokenized code lines.
     * @param array $output    Expected function output.
     * @param bool  $withTheme Whether or not the mock should act as if themes
     *                         have been registered or not.
     *
     * @return array
     */
    public function testColorLines($input, $expected, $withTheme = true)
    {
        $method = new ReflectionMethod('PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter', 'colorLines');
        $method->setAccessible(true);

        $highlighter = new Highlighter($this->getConsoleColorMock($withTheme));
        $output = $method->invoke($highlighter, $input);
        $method->setAccessible(false);

        $this->assertSame($expected, $output);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function dataColorLines()
    {
        return array(
            'No lines' => array(
                'input'    => array(),
                'expected' => array(),
            ),
            'With theme' => array(
                'input'     => self::$input,
                'expected'  => array(
                    7 => '<token_keyword>function </token_keyword><token_default>bar</token_default><token_keyword>(</token_keyword><token_default>$param</token_default><token_unknown>) </token_unknown><token_keyword>{}</token_keyword>',
                ),
                'withTheme' => true,
            ),
            'Without theme' => array(
                'input'     => self::$input,
                'expected'  => array(
                    7 => 'function bar($param) {}',
                ),
                'withTheme' => false,
            ),
        );
    }

    /**
     * Integration test for the colorLines() method.
     */
    public function testColorLinesWithRealColors()
    {
        $expected = array(
            7 => "\033[32mfunction \033[0m\033[39mbar\033[0m\033[32m(\033[0m\033[39m\$param\033[0m) \033[32m{}\033[0m",
        );

        $color = new ConsoleColor();
        $color->setForceStyle(true);

        $method = new ReflectionMethod('PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter', 'colorLines');
        $method->setAccessible(true);

        $highlighter = new Highlighter($color);
        $output = $method->invoke($highlighter, self::$input);
        $method->setAccessible(false);

        $this->assertSame($expected, $output);
    }
}
