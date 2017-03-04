<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\WhileCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\WhileCompiler
 */
class WhileCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        $this->expectMessageToBeThrown(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to while compiler.'
        );

        $whileCompiler = new WhileCompiler(new Compiler());
        $whileCompiler->compileNode(new ElementNode());
    }
}
