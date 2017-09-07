<?php

namespace Phug\Test\Compiler\NodeCompiler;

use Phug\Compiler;
use Phug\Compiler\NodeCompiler\MixinCallNodeCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\NodeCompiler\MixinCallNodeCompiler
 */
class MixinCallNodeCompilerTest extends AbstractCompilerTest
{
    /**
     * @group i
     * @group mixins
     * @covers ::<public>
     * @covers ::proceedBlocks
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::<public>
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::compileAnonymousBlock
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::compileNamedBlock
     * @covers \Phug\Compiler\NodeCompiler\MixinNodeCompiler::<public>
     * @covers \Phug\Compiler\NodeCompiler\MixinNodeCompiler::containsMixinCall
     * @covers \Phug\Compiler::getMixins
     * @covers \Phug\Compiler::replaceBlock
     * @covers \Phug\Compiler::requireMixin
     */
    public function testCompile()
    {
        $this->assertRenderFile(
            [
                '<section>',
                '<div class="ab">',
                '<div class="a"></div>',
                '<div class="b"></div>',
                '</div>',
                '<div>',
                '</div>',
                '</section>',
                '<div>bar</div>',
                '<article>append</article>',
                '<div class="ab">',
                '<div class="a">a</div>',
                '<div class="b">b</div>',
                '</div>',
                '<div>',
                '<h1>1</h1>',
                '</div>',
                '<article>prepend</article>',
                '<p>footer-foo</p>',
                '<p class="biz">bar</p>',
                '<div>footer</div>',
            ],
            __DIR__.'/../../../templates/mixins-test.pug'
        );
    }

    /**
     * @group mixins
     * @covers ::<public>
     */
    public function testCompileVariadicMixin()
    {
        $this->assertRender(
            [
                '<p>1</p>',
                '<i>2</i>',
                '<i>3</i>',
            ],
            [
                'mixin variadicMixin($a, ...$b)'."\n",
                '  p=$a'."\n",
                '  each $c in $b'."\n",
                '    i=$c'."\n",
                '+variadicMixin(1, 2, 3)',
            ]
        );
    }

    /**
     * @group mixins
     * @covers ::<public>
     * @covers ::proceedBlocks
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::<public>
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::compileAnonymousBlock
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::compileNamedBlock
     * @covers \Phug\Compiler\NodeCompiler\MixinNodeCompiler::<public>
     * @covers \Phug\Compiler\NodeCompiler\MixinNodeCompiler::containsMixinCall
     * @covers \Phug\Compiler::getMixins
     * @covers \Phug\Compiler::replaceBlock
     * @covers \Phug\Compiler\Util\PhpUnwrap::<public>
     */
    public function testDoubleBlock()
    {
        $compiler = new Compiler();
        file_put_contents(__DIR__.'/../../../../temp.php', $compiler->compileFile(__DIR__.'/../../../templates/mixin-double-block.pug'));
        $this->assertRenderFile(
            [
                '<header>HelloHello</header>',
                '<footer>ByeBye</footer>',
            ],
            __DIR__.'/../../../templates/mixin-double-block.pug'
        );
    }

    /**
     * @group mixins
     * @covers ::<public>
     * @covers ::compileDynamicMixin
     * @covers \Phug\Compiler::enableDynamicMixins
     * @covers \Phug\Compiler::compileDocument
     * @covers \Phug\Compiler::convertBlocksToDynamicCalls
     */
    public function testDynamicMixins()
    {
        $this->assertRender(
            [
                '<div>bar</div>',
            ],
            [
                'mixin bar'."\n",
                '  div bar'."\n",
                '+#{$foo}',
            ],
            [],
            [
                'foo' => 'bar',
            ]
        );
        $this->setUp();
        $this->assertRender(
            [
                '<div class="foo" bar="biz">',
                '1#2#3-4<em>Message</em>',
                '</div>',
                '<div bar="biz">',
                '1#2#3-4<em>Message</em>',
                '</div>',
                '<p>42</p>',
            ],
            [
                '- $bar = 40'."\n",
                'mixin bar(a, b, ...c)'."\n",
                '  - $bar++'."\n",
                '  div&attributes($attributes)'."\n",
                '    =$a."#".$b."#".implode("-", $c)'."\n",
                '    block'."\n",
                '+#{$foo}(1, 2, 3, 4).foo(bar="biz")'."\n",
                '  em Message'."\n",
                '+#{$foo}(1, 2, 3, 4)&attributes(["bar" => "biz" ])'."\n",
                '  em Message'."\n",
                'p=$bar',
            ],
            [],
            [
                'foo' => 'bar',
            ]
        );
    }

    /**
     * @group i
     * @covers ::<public>
     * @covers ::proceedBlocks
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::<public>
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::compileAnonymousBlock
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::compileNamedBlock
     * @covers \Phug\Compiler\NodeCompiler\MixinNodeCompiler::<public>
     * @covers \Phug\Compiler\NodeCompiler\MixinNodeCompiler::containsMixinCall
     * @covers \Phug\Compiler::getMixins
     * @covers \Phug\Compiler::replaceBlock
     * @covers \Phug\Compiler::requireMixin
     */
    public function testMixinAttributes()
    {
        $this->enableJsPhpize();
        $this->assertRenderFile(
            preg_replace('/(\S)\/>/', '$1 />',
                preg_replace('/\n\s*/', '',
                    file_get_contents(__DIR__.'/../../../templates/mixin.attrs.html')
                )
            ),
            __DIR__.'/../../../templates/mixin.attrs.pug'
        );
    }

    /**
     * @covers ::<public>
     * @covers ::proceedBlocks
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::<public>
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::compileAnonymousBlock
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::compileNamedBlock
     * @covers \Phug\Compiler\NodeCompiler\MixinNodeCompiler::<public>
     * @covers \Phug\Compiler\NodeCompiler\MixinNodeCompiler::containsMixinCall
     * @covers \Phug\Compiler::getMixins
     * @covers \Phug\Compiler::replaceBlock
     * @covers \Phug\Compiler::requireMixin
     */
    public function testMixinBlocks()
    {
        $this->enableJsPhpize();
        $this->assertRenderFile(
            preg_replace('/(\S)\/>/', '$1 />',
                preg_replace('/\n\s*/', '',
                    file_get_contents(__DIR__.'/../../../templates/mixin.blocks.html')
                )
            ),
            __DIR__.'/../../../templates/mixin.blocks.pug'
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
            'given to mixin call compiler.'
        );

        $mixinCallCompiler = new MixinCallNodeCompiler(new Compiler());
        $mixinCallCompiler->compileNode(new ElementNode());
    }

    /**
     * @covers            ::<public>
     * @expectedException \InvalidArgumentException
     */
    public function testUnknownMixin()
    {
        $this->expectMessageToBeThrown(
            'Unknown undef mixin called.'
        );

        $php = (new Compiler([
            'debug' => true,
        ]))->compile('+undef()');
        eval('?>'.$php);
    }
}
