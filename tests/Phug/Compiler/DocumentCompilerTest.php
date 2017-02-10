<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\DocumentCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\DoctypeCompiler
 */
class DocumentCompilerTest extends AbstractCompilerTest
{
    /**
    * @covers                   ::<public>
    * @expectedException        Phug\CompilerException
    * @expectedExceptionMessage Unexpected Phug\Parser\Node\ElementNode
    * @expectedExceptionMessage given to document compiler.
    */
    public function testException()
    {
        $documentCompiler = new DocumentCompiler(new Compiler());
        $documentCompiler->compileNode(new ElementNode());
    }
}
