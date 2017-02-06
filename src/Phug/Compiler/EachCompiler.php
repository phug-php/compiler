<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\EachNode;
use Phug\Parser\NodeInterface;

class EachCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof EachNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to each compiler.'
            );
        }

        return new MarkupElement('to-do-each');
    }
}
