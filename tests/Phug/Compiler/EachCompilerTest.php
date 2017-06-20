<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\EachCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass Phug\Compiler\EachCompiler
 */
class EachCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::<public>
     * @covers ::compileLoop
     * @covers \Phug\AbstractStatementNodeCompiler::wrapStatement
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
                'each $item in $items'."\n",
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
                'each $item in $items'."\n",
                '  p?!=$item'."\n",
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
                'each $item, $key in $items'."\n",
                '  p?!=$item',
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
            'given to each compiler.'
        );

        $eachCompiler = new EachCompiler(new Compiler());
        $eachCompiler->compileNode(new ElementNode());
    }
}
