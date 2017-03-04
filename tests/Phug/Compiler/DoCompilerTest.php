<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\DoCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\DoCompiler
 */
class DoCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        $this->expectMessageToBeThrown(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to do compiler.'
        );

        $doCompiler = new DoCompiler(new Compiler());
        $doCompiler->compileNode(new ElementNode());
    }
}
