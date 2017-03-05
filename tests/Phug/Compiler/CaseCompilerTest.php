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
        $this->expectMessageToBeThrown(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to case compiler.'
        );

        $caseCompiler = new CaseCompiler(new Compiler());
        $caseCompiler->compileNode(new ElementNode());
    }

    /**
     * @covers ::<public>
     * @covers \Phug\Compiler\WhenCompiler::<public>
     * @covers \Phug\AbstractStatementNodeCompiler::<public>
     */
    public function testCompile()
    {
        $this->assertCompile(
            [
                '<?php switch ($foo) { ?>',
                '<?php case 0: case 1: ?>',
                '<p>Hello</p>',
                '<?php break; default: ?>',
                '<p>Bye</p>',
                '<?php } ?>',
            ],
            [
                'case $foo'."\n",
                '  when 0'."\n",
                '  when 1'."\n",
                '    p Hello'."\n",
                '  default'."\n",
                '    p Bye',
            ]
        );
    }
}
