<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\DoNode;
use Phug\Parser\NodeInterface;

class DoCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof DoNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to do compiler.'
            );
        }

        return new MarkupElement('to-do-do');
    }
}
