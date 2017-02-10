<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\AttributeCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\AttributeCompiler
 */
class AttributeCompilerTest extends AbstractCompilerTest
{
    /**
    * @covers                   ::<public>
    * @expectedException        Phug\CompilerException
    * @expectedExceptionMessage Unexpected Phug\Parser\Node\ElementNode
    * @expectedExceptionMessage given to attribute compiler.
    */
    public function testException()
    {
        $attributeCompiler = new AttributeCompiler(new Compiler());
        $attributeCompiler->compileNode(new ElementNode());
    }
}
