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
                '<section></section>',
            ],
            __DIR__.'/../../templates/mixins-test.pug'
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
