<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\ExpressionCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\DoctypeCompiler
 */
class ExpressionCompilerTest extends AbstractCompilerTest
{
    /**
    * @covers                   ::<public>
    * @expectedException        Phug\CompilerException
    * @expectedExceptionMessage Unexpected Phug\Parser\Node\ElementNode
    * @expectedExceptionMessage given to expression compiler.
    */
    public function testException()
    {
        $expressionCompiler = new ExpressionCompiler(new Compiler());
        $expressionCompiler->compileNode(new ElementNode());
    }
}
