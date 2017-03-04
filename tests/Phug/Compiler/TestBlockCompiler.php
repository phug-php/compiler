<?php

namespace Phug\Test\Compiler;

use Phug\Compiler\Block;
use Phug\Compiler\BlockCompiler;
use Phug\Parser\NodeInterface;

class TestBlockCompiler extends BlockCompiler
{
    public function compileNode(NodeInterface $node)
    {
        $blocks = &$this->getCompiler()->getBlocksByName('foo');
        $blocks[] = 'bar';

        return new Block();
    }
}
