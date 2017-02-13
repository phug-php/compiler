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
     * @covers ::<public>
     */
    public function testCompile()
    {
        $this->assertCompile('<input name="a" />', 'input(name="a")');
        $this->assertCompile('<input name="<a>" />', 'input(name!="<a>")');
        $this->assertCompile('<input name="&lt;a&gt;" />', 'input(name="<a>")');
        $this->assertCompile([
            '<img ',
            'src="<?= htmlspecialchars("foo.$png") ?>" ',
            'alt="$bar" ',
            'width="<?= htmlspecialchars(get_width("foo.png")) ?>" ',
            'height="30" ',
            'data-ratio="0.54" ',
            'data-code="16205" />',
        ], [
            'img(',
            'src?="foo.$png" ',
            'alt=\'$bar\' ',
            'width=get_width("foo.png") ',
            'height=30 ',
            'data-ratio=0.54 ',
            'data-code=0x3f4d)'
        ]);
        $this->assertCompile(
            '<img src="<?= (isset($image) ? $image : \'\') ?>" />',
            'img(src!=$image)'
        );
    }

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
