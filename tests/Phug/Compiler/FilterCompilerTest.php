<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\FilterCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\DoctypeCompiler
 */
class FilterCompilerTest extends AbstractCompilerTest
{
    /**
    * @covers                   ::<public>
    * @expectedException        Phug\CompilerException
    * @expectedExceptionMessage Unexpected Phug\Parser\Node\ElementNode
    * @expectedExceptionMessage given to filter compiler.
    */
    public function testException()
    {
        $filterCompiler = new FilterCompiler(new Compiler());
        $filterCompiler->compileNode(new ElementNode());
    }
}
