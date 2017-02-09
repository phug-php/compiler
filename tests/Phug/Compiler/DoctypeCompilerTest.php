<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\DoctypeCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\DoctypeCompiler
 */
class DoctypeCompilerTest extends AbstractCompilerTest
{
    /**
    * @covers                   ::<public>
    * @expectedException        Phug\CompilerException
    * @expectedExceptionMessage Unexpected Phug\Parser\Node\ElementNode
    * @expectedExceptionMessage given to doctype compiler.
    */
    public function testException()
    {
        $doctypeCompiler = new DoctypeCompiler(new Compiler());
        $doctypeCompiler->compileNode(new ElementNode());
    }
}
