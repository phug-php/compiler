<?php

namespace Phug\Test\Compiler;

use Phug\Compiler\Block;
use Phug\Compiler\BlockCompiler;
use Phug\Formatter\ElementInterface;
use Phug\Parser\NodeInterface;

class TestBlockCompiler extends BlockCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        $blocks = &$this->getCompiler()->getBlocksByName('foo');
        $blocks[] = 'bar';

        return new Block();
    }
}
