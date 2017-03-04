<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\VariableCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\VariableCompiler
 */
class VariableCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        $this->expectMessageToBeThrown(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to variable compiler.'
        );

        $variableCompiler = new VariableCompiler(new Compiler());
        $variableCompiler->compileNode(new ElementNode());
    }
}
