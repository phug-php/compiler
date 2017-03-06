<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\DocumentCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\DocumentCompiler
 */
class DocumentCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::<public>
     */
    public function testCompile()
    {
        $this->assertCompile('<html></html>', 'html');
    }

    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        $this->expectMessageToBeThrown(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to document compiler.'
        );

        $documentCompiler = new DocumentCompiler(new Compiler());
        $documentCompiler->compileNode(new ElementNode());
    }
}
