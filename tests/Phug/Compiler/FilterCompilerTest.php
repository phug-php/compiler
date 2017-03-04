<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\FilterCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\FilterCompiler
 */
class FilterCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        $this->expectMessageToBeThrown(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to filter compiler.'
        );

        $filterCompiler = new FilterCompiler(new Compiler());
        $filterCompiler->compileNode(new ElementNode());
    }
}
