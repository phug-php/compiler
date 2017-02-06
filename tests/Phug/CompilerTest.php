<?php

namespace Phug\Test;

use Phug\Compiler;

/**
 * @coversDefaultClass Phug\Compiler
 */
class CompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::<public>
     */
    public function testCompile()
    {
        // Single tag
        self::assertSame('<html></html>', $this->compiler->compile('html'));

        // Children
        self::assertSame('<html><head></head><body></body></html>', $this->compiler->compile("html\n  head\n  body"));
    }
}
