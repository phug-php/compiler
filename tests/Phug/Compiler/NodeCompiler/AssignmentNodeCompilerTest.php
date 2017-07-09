<?php

namespace Phug\Test\Compiler\NodeCompiler;

use Phug\Compiler;
use Phug\Compiler\NodeCompiler\AssignmentNodeCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\NodeCompiler\AssignmentNodeCompiler
 */
class AssignmentNodeCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::<public>
     * @covers \Phug\Compiler\NodeCompiler\ElementNodeCompiler::compileNode
     */
    public function testCompile()
    {
        $this->assertRender(
            '<a href="#"></a>',
            'a&attributes(["href" => "#"])'
        );
        $this->assertRender(
            '<a class="bar fiz foo biz"></a>',
            'a.foo(class=["bar", "biz"])&attributes(["class" => "bar fiz"])'
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
            'given to assignment compiler.'
        );

        $assignmentCompiler = new AssignmentNodeCompiler(new Compiler());
        $assignmentCompiler->compileNode(new ElementNode());
    }
}
