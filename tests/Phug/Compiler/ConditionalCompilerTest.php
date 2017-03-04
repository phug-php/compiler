<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\ConditionalCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\ConditionalCompiler
 */
class ConditionalCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        self::expectExceptionMessage(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to conditional compiler.'
        );

        $conditionalCompiler = new ConditionalCompiler(new Compiler());
        $conditionalCompiler->compileNode(new ElementNode());
    }
}
