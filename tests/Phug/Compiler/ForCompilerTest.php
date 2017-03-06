<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\ForCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\ForCompiler
 */
class ForCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::<public>
     * @covers \Phug\Compiler\EachCompiler::<public>
     * @covers \Phug\AbstractStatementNodeCompiler::<public>
     */
    public function testCompile()
    {
        $this->assertCompile(
            [
                '<?php foreach ($items as $item) { ?>',
                '<p><?= $item ?></p>',
                '<?php } ?>',
            ],
            [
                'for $item in $items'."\n",
                '  p?!=$item',
            ]
        );
        $this->assertCompile(
            [
                '<?php foreach ($items as $key => $item) { ?>',
                '<p><?= $item ?></p>',
                '<?php } ?>',
            ],
            [
                'for $item, $key in $items'."\n",
                '  p?!=$item',
            ]
        );
        $this->assertCompile(
            [
                '<?php foreach ($items as $key => $__none) { ?>',
                '<p><?= $key ?></p>',
                '<?php } ?>',
            ],
            [
                'for $key of $items'."\n",
                '  p?!=$key',
            ]
        );
        $this->assertCompile(
            [
                '<?php foreach ($items as $key => $item) { ?>',
                '<p><?= $item ?></p>',
                '<?php } ?>',
            ],
            [
                'for $key, $item of $items'."\n",
                '  p?!=$item',
            ]
        );
        $this->assertCompile(
            [
                '<?php for ($i = 0; $i < 5; $i++) { ?>',
                '<p><?= $i ?></p>',
                '<?php } ?>',
            ],
            [
                'for $i = 0; $i < 5; $i++'."\n",
                '  p?!=$i',
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
            'given to for compiler.'
        );

        $forCompiler = new ForCompiler(new Compiler());
        $forCompiler->compileNode(new ElementNode());
    }
}
