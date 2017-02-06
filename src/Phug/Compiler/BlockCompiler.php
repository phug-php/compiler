<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\BlockNode;
use Phug\Parser\NodeInterface;

class BlockCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof BlockNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to block compiler.'
            );
        }

        return new MarkupElement('to-do-block');
    }
}
