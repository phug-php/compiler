<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\AttributeListNode;
use Phug\Parser\NodeInterface;

class AttributeListCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof AttributeListNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to attribute list compiler.'
            );
        }

        return new MarkupElement('to-do-attribute-list');
    }
}