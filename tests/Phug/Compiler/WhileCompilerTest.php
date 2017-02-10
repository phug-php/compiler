<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\WhileCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\DoctypeCompiler
 */
class WhileCompilerTest extends AbstractCompilerTest
{
    /**
    * @covers                   ::<public>
    * @expectedException        Phug\CompilerException
    * @expectedExceptionMessage Unexpected Phug\Parser\Node\ElementNode
    * @expectedExceptionMessage given to while compiler.
    */
    public function testException()
    {
        $whileCompiler = new WhileCompiler(new Compiler());
        $whileCompiler->compileNode(new ElementNode());
    }
}
