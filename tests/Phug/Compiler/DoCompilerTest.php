<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\DoCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\DoCompiler
 */
class DoCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers                   ::<public>
     * @expectedException        Phug\CompilerException
     * @expectedExceptionMessage Unexpected Phug\Parser\Node\ElementNode
     * @expectedExceptionMessage given to do compiler.
     */
    public function testException()
    {
        $doCompiler = new DoCompiler(new Compiler());
        $doCompiler->compileNode(new ElementNode());
    }
}
