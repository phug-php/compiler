<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\WhenCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\WhenCompiler
 */
class WhenCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        self::expectExceptionMessage(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to when compiler.'
        );

        $whenCompiler = new WhenCompiler(new Compiler());
        $whenCompiler->compileNode(new ElementNode());
    }
}
