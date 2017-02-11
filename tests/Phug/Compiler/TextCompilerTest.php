<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\TextCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\TextCompiler
 */
class TextCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers                   ::<public>
     * @expectedException        Phug\CompilerException
     * @expectedExceptionMessage Unexpected Phug\Parser\Node\ElementNode
     * @expectedExceptionMessage given to text compiler.
     */
    public function testException()
    {
        $textCompiler = new TextCompiler(new Compiler());
        $textCompiler->compileNode(new ElementNode());
    }
}
