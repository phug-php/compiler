<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\TextCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\TextCompiler
 */
class TextCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::<public>
     */
    public function testText()
    {
        $this->assertCompile('Hello', '| Hello');
    }

    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        $this->expectMessageToBeThrown(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to text compiler.'
        );

        $textCompiler = new TextCompiler(new Compiler());
        $textCompiler->compileNode(new ElementNode());
    }
}
