<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\ConditionalCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\ConditionalCompiler
 */
class ConditionalCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        $this->expectMessageToBeThrown(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to conditional compiler.'
        );

        $conditionalCompiler = new ConditionalCompiler(new Compiler());
        $conditionalCompiler->compileNode(new ElementNode());
    }

    /**
     * @covers ::<public>
     */
    public function testCompile()
    {
        $this->assertCompile(
            [
                '<?php if ($foo > 50) { ?>',
                '<p>Huge foo</p>',
                '<?php } elseif ($foo > 20) { ?>',
                '<p>Big foo</p>',
                '<?php } elseif ($foo > 10) { ?>',
                '<p>Medium foo</p>',
                '<?php } else { ?>',
                '<p>Small foo</p>',
                '<?php } if (!($foo % 1)) { ?>',
                '<p>Even foo</p>',
                '<?php } else { ?>',
                '<p>Odd foo</p>',
                '<?php } ?>',
            ],
            [
                'if $foo > 50'."\n",
                '  p Huge foo'."\n",
                'else if $foo > 20'."\n",
                '  p Big foo'."\n",
                'elseif $foo > 10'."\n",
                '  p Medium foo'."\n",
                'else'."\n",
                '  p Small foo'."\n",
                'unless $foo % 1'."\n",
                '  p Even foo'."\n",
                'else'."\n",
                '  p Odd foo',
            ]
        );
    }
}
