<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\WhileNode;
use Phug\Parser\NodeInterface;

class WhileCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof WhileNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to while compiler.'
            );
        }

        return new MarkupElement('to-do-while');
    }
}
