<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\CaseCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\DoctypeCompiler
 */
class CaseCompilerTest extends AbstractCompilerTest
{
    /**
    * @covers                   ::<public>
    * @expectedException        Phug\CompilerException
    * @expectedExceptionMessage Unexpected Phug\Parser\Node\ElementNode
    * @expectedExceptionMessage given to case compiler.
    */
    public function testException()
    {
        $caseCompiler = new CaseCompiler(new Compiler());
        $caseCompiler->compileNode(new ElementNode());
    }
}
