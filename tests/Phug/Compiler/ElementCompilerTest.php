<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\ElementCompiler;
use Phug\Parser\Node\DoNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\ElementCompiler
 */
class ElementCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::<public>
     */
    public function testCompile()
    {
        $this->assertCompile('<section><input /></section>', 'section: input');
    }

    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        self::expectExceptionMessage(
            'Unexpected Phug\Parser\Node\DoNode '.
            'given to element compiler.'
        );

        $elementCompiler = new ElementCompiler(new Compiler());
        $elementCompiler->compileNode(new DoNode());
    }
}
