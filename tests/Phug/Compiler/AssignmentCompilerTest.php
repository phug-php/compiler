<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\AssignmentCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\AssignmentCompiler
 */
class AssignmentCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::<public>
     */
    public function testCompile()
    {
        $this->assertRender('<a href="#"></a>', 'a&attributes(["href" => "#"])');
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

        $assignmentCompiler = new AssignmentCompiler(new Compiler());
        $assignmentCompiler->compileNode(new ElementNode());
    }
}
