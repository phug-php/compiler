<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\VariableCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\DoctypeCompiler
 */
class VariableCompilerTest extends AbstractCompilerTest
{
    /**
    * @covers                   ::<public>
    * @expectedException        Phug\CompilerException
    * @expectedExceptionMessage Unexpected Phug\Parser\Node\ElementNode
    * @expectedExceptionMessage given to variable compiler.
    */
    public function testException()
    {
        $variableCompiler = new VariableCompiler(new Compiler());
        $variableCompiler->compileNode(new ElementNode());
    }
}
