<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\BlockNode;
use Phug\Parser\NodeInterface;

class BlockCompiler extends AbstractNodeCompiler
{
    protected function compileNamedBlock(Block $block, BlockNode $node)
    {
        return $block->proceedNode($node);
    }

    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof BlockNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to block compiler.'
            );
        }

        $name = $node->getName();

        if (!$name) {
            return new MarkupElement('to-do-anonymous-block');
        }

        return $this->compileNamedBlock($this->getCompiler()->getNamedBlock($name), $node);
    }
}
