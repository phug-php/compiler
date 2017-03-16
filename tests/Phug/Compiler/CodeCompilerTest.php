<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\CodeCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\CodeCompiler
 */
class CodeCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::<public>
     * @covers \Phug\AbstractNodeCompiler::getTextChildren
     */
    public function testCompile()
    {
        $this->assertCompile(
            '<?php $foo = 4 ?>',
            '- $foo = 4'
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
            'given to code compiler.'
        );

        $expressionCompiler = new CodeCompiler(new Compiler());
        $expressionCompiler->compileNode(new ElementNode());
    }
}
