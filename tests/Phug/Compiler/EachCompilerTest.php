<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\EachCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\EachCompiler
 */
class EachCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers                   ::<public>
     * @expectedException        Phug\CompilerException
     * @expectedExceptionMessage Unexpected Phug\Parser\Node\ElementNode
     * @expectedExceptionMessage given to each compiler.
     */
    public function testException()
    {
        $eachCompiler = new EachCompiler(new Compiler());
        $eachCompiler->compileNode(new ElementNode());
    }
}
