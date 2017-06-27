<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\MixinCallCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\MixinCallCompiler
 */
class MixinCallCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::<public>
     * @covers ::proceedBlocks
     * @covers \Phug\Compiler\BlockCompiler::<public>
     * @covers \Phug\Compiler\BlockCompiler::compileAnonymousBlock
     * @covers \Phug\Compiler\BlockCompiler::compileNamedBlock
     * @covers \Phug\Compiler\MixinCompiler::<public>
     * @covers \Phug\Compiler::getMixins
     * @covers \Phug\Compiler::replaceBlock
     */
    public function testCompile()
    {
        $this->assertRenderFile(
            [
                '<section>'.PHP_EOL,
                '  <div class="ab">'.PHP_EOL,
                '    <div class="a"></div>'.PHP_EOL,
                '    <div class="b"></div>'.PHP_EOL,
                '  </div>'.PHP_EOL,
                '  <div>'.PHP_EOL,
                '  </div>'.PHP_EOL,
                '</section>'.PHP_EOL,
                '<div>bar</div>'.PHP_EOL,
                '<article>append</article>'.PHP_EOL,
                '<div class="ab">'.PHP_EOL,
                '  <div class="a">a</div>'.PHP_EOL,
                '  <div class="b">b</div>'.PHP_EOL,
                '</div>'.PHP_EOL,
                '<div>'.PHP_EOL,
                '  <h1>1</h1>'.PHP_EOL,
                '</div>'.PHP_EOL,
                '<article>prepend</article>'.PHP_EOL,
                '<p>footer-foo</p>'.PHP_EOL,
                '<p class="biz">bar</p>'.PHP_EOL,
                '<div>footer</div>'.PHP_EOL,
            ],
            __DIR__.'/../../templates/mixins-test.pug',
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
            'given to mixin call compiler.'
        );

        $mixinCallCompiler = new MixinCallCompiler(new Compiler());
        $mixinCallCompiler->compileNode(new ElementNode());
    }

    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testUnknownMixin()
    {
        $this->expectMessageToBeThrown(
            'Unknown undef mixin called.'
        );

        (new Compiler())->compile('+undef()');
    }
}
