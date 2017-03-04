<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\FilterNode;
use Phug\Parser\NodeInterface;

class FilterCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof FilterNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to filter compiler.'
            );
        }

        return new MarkupElement('to-do-filter');
    }
}
