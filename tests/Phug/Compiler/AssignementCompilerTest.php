<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\AssignementCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\AssignementCompiler
 */
class AssignementCompilerTest extends AbstractCompilerTest
{
    /**
    * @covers                   ::<public>
    * @expectedException        Phug\CompilerException
    * @expectedExceptionMessage Unexpected Phug\Parser\Node\ElementNode
    * @expectedExceptionMessage given to assignement compiler.
    */
    public function testException()
    {
        $assignementCompiler = new AssignementCompiler(new Compiler());
        $assignementCompiler->compileNode(new ElementNode());
    }
}
