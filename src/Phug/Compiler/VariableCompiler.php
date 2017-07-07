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
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to variable compiler.',
                $node
            );
        }

        /**
         * @var VariableNode $node
         */
        $count = $node->getChildCount();
        $child = $count === 1 ? $node->getChildAt(0) : null;
        if (!($child instanceof ExpressionNode)) {
            $this->getCompiler()->throwException(
                'Variable should be followed by exactly 1 expression.',
                $node
            );
        }

        $compiler = $this->getCompiler();

        return $this->createVariable(
            $node,
            $node->getName(),
            $compiler->compileNode($child, $parent)
        );
    }
}
