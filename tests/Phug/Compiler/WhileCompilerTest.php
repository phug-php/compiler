<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\WhileCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\WhileCompiler
 */
class WhileCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::<public>
     * @covers \Phug\AbstractStatementNodeCompiler::<public>
     * @covers \Phug\Compiler\WhileCompiler::<public>
     */
    public function testCompile()
    {
        $this->assertCompile(
            [
                '<?php do { ?>',
                '<p>foo</p>',
                '<?php } while ($foo > 20); ?>',
            ],
            [
                'do'."\n",
                '  p foo'."\n",
                'while $foo > 20',
            ]
        );
        $this->assertCompile(
            [
                '<?php while ($foo > 20) { ?>',
                '<p>foo</p>',
                '<?php } ?>',
            ],
            [
                'while $foo > 20'."\n",
                '  p foo',
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
            'given to while compiler.'
        );

        $whileCompiler = new WhileCompiler(new Compiler());
        $whileCompiler->compileNode(new ElementNode());
    }

    /**
     * @covers            ::<public>
     * @covers            \Phug\Compiler\DoCompiler::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testDoAndWhileSeededException()
    {
        $this->expectMessageToBeThrown(
            'While statement cannot have children and come after a do statement.'
        );

        $compiler = new Compiler();
        $compiler->compile(
            'do'."\n".
            '  div A'."\n".
            'while a()'."\n".
            '  div B'
        );
    }
}
