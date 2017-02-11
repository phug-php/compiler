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
     * @expectedExceptionMessage given to assignementlist compiler.
     */
    public function testException()
    {
        $assignementlistCompiler = new AssignementListCompiler(new Compiler());
        $assignementlistCompiler->compileNode(new ElementNode());
    }
}
