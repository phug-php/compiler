<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\VariableNode;
use Phug\Parser\NodeInterface;

class VariableCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof VariableNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to variable compiler.'
            );
        }

        return new MarkupElement('to-do-variable');
    }
}
