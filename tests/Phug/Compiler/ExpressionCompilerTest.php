<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\ExpressionCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\ExpressionCompiler
 */
class ExpressionCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        $this->expectMessageToBeThrown(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to expression compiler.'
        );

        $expressionCompiler = new ExpressionCompiler(new Compiler());
        $expressionCompiler->compileNode(new ElementNode());
    }
}
