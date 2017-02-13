<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\AttributeListCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\AttributeListCompiler
 */
class AttributeListCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers                   ::<public>
     * @expectedException        \Phug\CompilerException
     * @expectedExceptionMessage Unexpected Phug\Parser\Node\ElementNode
     * @expectedExceptionMessage given to attribute list compiler.
     */
    public function testException()
    {
        $attributelistCompiler = new AttributeListCompiler(new Compiler());
        $attributelistCompiler->compileNode(new ElementNode());
    }
}
