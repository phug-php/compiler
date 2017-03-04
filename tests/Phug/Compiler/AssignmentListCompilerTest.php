<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\AssignmentListCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\AssignmentListCompiler
 */
class AssignmentListCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        self::expectExceptionMessage(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to assignment list compiler.'
        );

        $assignmentListCompiler = new AssignmentListCompiler(new Compiler());
        $assignmentListCompiler->compileNode(new ElementNode());
    }
}
