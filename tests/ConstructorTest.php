<?php

namespace PHP_Parallel_Lint\PhpConsoleHighlighter\Test;

use PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor;
use PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter;

/**
 * @covers PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter::__construct
 */
class ConstructorTest extends HighlighterTestCase
{
    /**
     * Test constructor, including integration with ConsoleColor.
     */
    public function testConstructor()
    {
        $color = new ConsoleColor();
        // Ensure that at least one style is already set so all branches get tested.
        $color->addTheme(Highlighter::TOKEN_DEFAULT, 'bg_light_red');

        $highlighter = new Highlighter($color);
        $themes      = $color->getThemes();

        $this->assertNotEmpty($themes);
        $this->assertCount(7, $themes);
    }
}
