<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\MixinCallCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\MixinCallCompiler
 */
class MixinCallCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers                   ::<public>
     * @expectedException        Phug\CompilerException
     * @expectedExceptionMessage Unexpected Phug\Parser\Node\ElementNode
     * @expectedExceptionMessage given to mixincall compiler.
     */
    public function testException()
    {
        $mixincallCompiler = new MixinCallCompiler(new Compiler());
        $mixincallCompiler->compileNode(new ElementNode());
    }
}
