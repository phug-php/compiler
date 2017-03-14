<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\ExpressionNode;
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

        /**
         * @var VariableNode $node
         */
        $count = $node->getChildCount();
        $child = $count === 1 ? $node->getChildAt(0) : null;
        if (!($child instanceof ExpressionNode)) {
            throw new CompilerException(
                'Variable should be followed by exactly 1 expression.'
            );
        }

        $compiler = $this->getCompiler();

        return $this->createVariable(
            $node->getName(),
            $compiler->compileNode($child, $parent)
        );
    }
}
