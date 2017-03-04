<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\ConditionalNode;
use Phug\Parser\NodeInterface;

class ConditionalCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof ConditionalNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to conditional compiler.'
            );
        }

        return new MarkupElement('to-do-conditional');
    }
}
