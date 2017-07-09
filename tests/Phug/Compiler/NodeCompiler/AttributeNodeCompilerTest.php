<?php

namespace Phug\Test\Compiler\NodeCompiler;

use Phug\Compiler;
use Phug\Compiler\NodeCompiler\AttributeNodeCompiler;
use Phug\Parser\Node\AttributeNode;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;
use stdClass;

/**
 * @coversDefaultClass \Phug\Compiler\NodeCompiler\AttributeNodeCompiler
 */
class AttributeNodeCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::<public>
     * @covers ::compileName
     * @covers ::compileValue
     * @covers \Phug\Compiler\NodeCompiler\ElementNodeCompiler::compileNode
     */
    public function testCompile()
    {
        $this->assertCompile('<input name="a" />', 'input(name="a")');
        $this->assertCompile('<input (name)="a" />', 'input((name)="a")');
        $this->assertCompile('<input (name)="a" />', 'input("(name)"="a")');
        $this->assertCompile('<input name="<a>" />', 'input(name!="<a>")');
        $this->assertCompile('<input name="name" />', 'input(name)');
        $this->assertCompile('<input name="&lt;a&gt;" />', 'input(name="<a>")');
        $this->assertCompile([
            '<img ',
            'src="<?= htmlspecialchars((is_array($_pug_temp = "foo.$png") || ',
            '(is_object($_pug_temp) && !method_exists($_pug_temp, "__toString")) ? ',
            'json_encode($_pug_temp) : strval($_pug_temp))) ?>" ',
            'alt="$bar" ',
            'width="<?= htmlspecialchars((is_array($_pug_temp = get_width("foo.png")) || ',
            '(is_object($_pug_temp) && !method_exists($_pug_temp, "__toString")) ? ',
            'json_encode($_pug_temp) : strval($_pug_temp))) ?>" ',
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
            '<img src="<?= (is_array($_pug_temp = (isset($image) ? $image : \'\')) || '.
            '(is_object($_pug_temp) && !method_exists($_pug_temp, "__toString")) ? '.
            'json_encode($_pug_temp) : strval($_pug_temp)) ?>" />',
            'img(src!=$image)'
        );
        $this->assertRender(
            '<a class="1 2 3" data-class="[1,2,3]"></a>',
            'a(class=[1,2,3], data-class=[1,2,3])'
        );
    }

    /**
     * @covers ::<public>
     */
    public function testUnescapedAttributes()
    {
        $this->assertRenderFile(
            [
                '<script type="text/x-template">'.PHP_EOL,
                '  <div id="user-<%= user.id %>">'.PHP_EOL,
                '    <h1><%= user.title %></h1>'.PHP_EOL,
                '  </div>'.PHP_EOL,
                '</script>'.PHP_EOL,
            ],
            __DIR__.'/../../../templates/attrs.unescaped.pug',
            [
                'pretty' => '  ',
            ]
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

        $attributeCompiler = new AttributeNodeCompiler(new Compiler());
        $attributeCompiler->compileNode(new ElementNode());
    }

    /**
     * @covers            ::compileValue
     * @expectedException \Phug\CompilerException
     */
    public function testAttributeException()
    {
        $this->expectMessageToBeThrown(
            'Attribute value can only be a string, a boolean or an expression, '.
            'stdClass given.'
        );

        $attributeCompiler = new AttributeNodeCompiler(new Compiler());
        $node = new AttributeNode();
        $node->setValue(new stdClass());
        $attributeCompiler->compileNode($node);
    }
}
