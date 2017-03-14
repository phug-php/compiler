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
     * @group i
     * @covers ::<public>
     * @covers \Phug\Compiler\BlockCompiler::<public>
     * @covers \Phug\Compiler\MixinCompiler::<public>
     */
    public function testCompile()
    {
        $this->assertRenderFile(
            [
                '<section>'."\n",
                '  <div class="ab">'."\n",
                '    <div class="a"></div>'."\n",
                '    <div class="b"></div>'."\n",
                '  </div>'."\n",
                '  <div></div>'."\n",
                '</section>'."\n",
                '<div>bar</div>'."\n",
                '<article>append</article>'."\n",
                '<div class="ab">'."\n",
                '  <div class="a"></div>'."\n",
                '  <div class="b"></div>'."\n",
                '</div>'."\n",
                '<div>'."\n",
                '  <h1>1</h1>'."\n",
                '</div>'."\n",
                '<article>prepend</article>'."\n",
                '<p>footer-foo</p>'."\n",
                '<p class="biz">bar</p>'."\n",
                '<div>footer</div>'."\n",
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
}
