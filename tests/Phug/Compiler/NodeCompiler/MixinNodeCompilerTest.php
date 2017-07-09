<?php

namespace Phug\Test\Compiler\NodeCompiler;

use Phug\Compiler;
use Phug\Compiler\MixinCompiler;
use Phug\Compiler\NodeCompiler\MixinNodeCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\NodeCompiler\MixinNodeCompiler
 */
class MixinNodeCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        $this->expectMessageToBeThrown(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to mixin compiler.'
        );

        $mixinCompiler = new MixinNodeCompiler(new Compiler());
        $mixinCompiler->compileNode(new ElementNode());
    }
}
