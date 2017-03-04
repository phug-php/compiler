<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\CaseCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\CaseCompiler
 */
class CaseCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        self::expectExceptionMessage(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to case compiler.'
        );

        $caseCompiler = new CaseCompiler(new Compiler());
        $caseCompiler->compileNode(new ElementNode());
    }
}
