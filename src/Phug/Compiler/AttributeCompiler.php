<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\AttributeNode;
use Phug\Parser\NodeInterface;

class AttributeCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof AttributeNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to attribute compiler.'
            );
        }

        return new MarkupElement('to-do-attribute');
    }
}
