<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\ForCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\ForCompiler
 */
class ForCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers                   ::<public>
     * @expectedException        \Phug\CompilerException
     * @expectedExceptionMessage Unexpected Phug\Parser\Node\ElementNode
     * @expectedExceptionMessage given to for compiler.
     */
    public function testException()
    {
        $forCompiler = new ForCompiler(new Compiler());
        $forCompiler->compileNode(new ElementNode());
    }
}
