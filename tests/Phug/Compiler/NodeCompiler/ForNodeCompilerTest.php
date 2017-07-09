<?php

namespace Phug\Test\Compiler\NodeCompiler;

use Phug\Compiler;
use Phug\Compiler\ForCompiler;
use Phug\Compiler\NodeCompiler\ForNodeCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\NodeCompiler\ForNodeCompiler
 */
class ForNodeCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::<public>
     * @covers \Phug\Compiler\NodeCompiler\EachNodeCompiler::<public>
     * @covers \Phug\Compiler\NodeCompiler\EachNodeCompiler::compileLoop
     * @covers \Phug\Compiler\NodeCompiler\AbstractStatementNodeCompiler::wrapStatement
     */
    public function testCompile()
    {
        $this->assertCompile(
            '<?php foreach ($items as $item) {} ?>',
            'for $item in $items'
        );
        $this->assertCompile(
            [
                '<?php foreach ($items as $item) { ?>',
                '<p><?= $item ?></p>',
                '<?php } ?>',
            ],
            [
                'for $item in $items'."\n",
                '  //- for each item of items'."\n",
                '  p?!=$item',
            ]
        );
        $this->assertCompile(
            [
                '<?php $__pug_temp_empty = true; foreach ($items as $item) { ?>',
                '<?php $__pug_temp_empty = false ?>',
                '<p><?= $item ?></p>',
                '<?php } ?>',
                '<?php if ($__pug_temp_empty) { ?>',
                '<p>no items</p>',
                '<?php } ?>',
            ],
            [
                'for $item in $items'."\n",
                '  p?!=$item'."\n",
                'else'."\n",
                '  p no items',
            ]
        );
        $this->assertCompile(
            [
                '<?php $__pug_temp_empty = true; foreach ($items as $item) { ?>',
                '<?php $__pug_temp_empty = false ?>',
                '<p><?= $item ?></p>',
                '<?php } ?>',
                '<?php if ($__pug_temp_empty) { ?>',
                '<p>no items</p>',
                '<?php } ?>',
            ],
            [
                'for $item in $items'."\n",
                '  p?!=$item'."\n",
                '//- comments does not count'."\n",
                'else'."\n",
                '  p no items',
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

        $forCompiler = new ForNodeCompiler(new Compiler());
        $forCompiler->compileNode(new ElementNode());
    }
}
