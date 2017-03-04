<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\AttributeCompiler;
use Phug\Parser\Node\AttributeNode;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;
use stdClass;

/**
 * @coversDefaultClass \Phug\Compiler\AttributeCompiler
 */
class AttributeCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::<public>
     * @covers ::compileName
     * @covers ::compileValue
     */
    public function testCompile()
    {
        $this->assertCompile('<input name="a" />', 'input(name="a")');
        $this->assertCompile('<input (name)="a" />', 'input((name)="a")');
        $this->assertCompile('<input (name)="a" />', 'input("(name)"="a")');
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
            'data-code=0x3f4d)',
        ]);
        $this->assertCompile(
            '<img src="<?= (isset($image) ? $image : \'\') ?>" />',
            'img(src!=$image)'
        );
    }

    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        $this->expectMessageToBeThrown(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to attribute compiler.'
        );

        $attributeCompiler = new AttributeCompiler(new Compiler());
        $attributeCompiler->compileNode(new ElementNode());
    }

    /**
     * @group i
     * @covers            ::compileValue
     * @expectedException \Phug\CompilerException
     */
    public function testAttributeException()
    {
        $this->expectMessageToBeThrown(
            'Attribute value can only be a string or an expression, '.
            'stdClass given.'
        );

        $attributeCompiler = new AttributeCompiler(new Compiler());
        $node = new AttributeNode();
        $node->setValue(new stdClass());
        $attributeCompiler->compileNode($node);
    }
}
