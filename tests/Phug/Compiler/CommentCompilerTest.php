<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\CommentCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\CommentCompiler
 */
class CommentCompilerTest extends AbstractCompilerTest
{
    /**
    * @covers                   ::<public>
    * @expectedException        Phug\CompilerException
    * @expectedExceptionMessage Unexpected Phug\Parser\Node\ElementNode
    * @expectedExceptionMessage given to comment compiler.
    */
    public function testException()
    {
        $commentCompiler = new CommentCompiler(new Compiler());
        $commentCompiler->compileNode(new ElementNode());
    }
}
