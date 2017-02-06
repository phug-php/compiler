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
        self::assertSame('<html></html>', $this->compiler->compile('html'));
    }
}
