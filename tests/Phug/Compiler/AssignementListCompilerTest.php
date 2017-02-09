<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\AssignementListCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\AssignementListCompiler
 */
class AssignementListCompilerTest extends AbstractCompilerTest
{
    /**
    * @covers                   ::<public>
    * @expectedException        Phug\CompilerException
    * @expectedExceptionMessage Unexpected Phug\Parser\Node\ElementNode
    * @expectedExceptionMessage given to doctype compiler.
    */
    public function testException()
    {
        $doctypeCompiler = new AssignementListCompiler(new Compiler());
        $doctypeCompiler->compileNode(new ElementNode());
    }
}
