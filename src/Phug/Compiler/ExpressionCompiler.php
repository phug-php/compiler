<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\ExpressionNode;
use Phug\Parser\NodeInterface;

class ExpressionCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof ExpressionNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to expression compiler.'
            );
        }

        return new MarkupElement('to-do-expression');
    }
}
