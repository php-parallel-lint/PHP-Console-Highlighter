<?php

namespace PHP_Parallel_Lint\PhpConsoleHighlighter\Test;

use PHPUnit\Framework\TestCase;

class HighlighterTestCase extends TestCase
{
    /**
     * Helper method mocking the Console Color Class.
     *
     * @param bool $withTheme Whether or not the mock should act as if themes
     *                        have been registered or not.
     *
     * @return \PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor
     */
    protected function getConsoleColorMock($withTheme = true)
    {
        $mock = method_exists($this, 'createMock')
            ? $this->createMock('\PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor')
            : $this->getMock('\PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor');

        $mock->expects($this->any())
            ->method('apply')
            ->will($this->returnCallback(function ($style, $text) use ($withTheme) {
                if ($withTheme) {
                    return "<$style>$text</$style>";
                } else {
                    return $text;
                }
            }));

        $mock->expects($this->any())
            ->method('hasTheme')
            ->will($this->returnValue($withTheme));

        return $mock;
    }
}
