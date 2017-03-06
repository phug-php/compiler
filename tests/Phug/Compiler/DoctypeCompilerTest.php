<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\DoctypeCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\DoctypeCompiler
 */
class DoctypeCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::<public>
     */
    public function testCompile()
    {
        $this->assertCompile(
            [
                '<!DOCTYPE html>',
            ],
            [
                'doctype html',
            ]
        );
    }

    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        $this->expectMessageToBeThrown(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to doctype compiler.'
        );

        $doctypeCompiler = new DoctypeCompiler(new Compiler());
        $doctypeCompiler->compileNode(new ElementNode());
    }
}
