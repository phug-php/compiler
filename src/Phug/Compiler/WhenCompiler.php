<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\WhenNode;
use Phug\Parser\NodeInterface;

class WhenCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof WhenNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to when compiler.'
            );
        }

        return new MarkupElement('to-do-when');
    }
}
