<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\EachCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\EachCompiler
 */
class EachCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        self::expectExceptionMessage(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to each compiler.'
        );

        $eachCompiler = new EachCompiler(new Compiler());
        $eachCompiler->compileNode(new ElementNode());
    }
}
