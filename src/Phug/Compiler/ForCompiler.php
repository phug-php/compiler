<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\ForNode;
use Phug\Parser\NodeInterface;

class ForCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof ForNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to for compiler.'
            );
        }

        return new MarkupElement('to-do-for');
    }
}
