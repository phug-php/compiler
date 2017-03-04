<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\CommentCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\CommentCompiler
 */
class CommentCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        $this->expectMessageToBeThrown(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to comment compiler.'
        );

        $commentCompiler = new CommentCompiler(new Compiler());
        $commentCompiler->compileNode(new ElementNode());
    }

    /**
     * @group i
     * @covers ::<public>
     */
    public function testCompile()
    {
        $this->assertCompile(
            '<!-- Comment -->',
            '// Comment'
        );
        $this->assertCompile(
            '',
            '//- Comment'
        );
    }
}
