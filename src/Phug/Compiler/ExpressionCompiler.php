<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\Formatter\Element\ExpressionElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\ExpressionNode;
use Phug\Parser\NodeInterface;

class ExpressionCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof ExpressionNode)) {
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to expression compiler.',
                $node
            );
        }

        /** @var ExpressionNode $element */
        $value = $node->getValue();
        $expression = new ExpressionElement($node, $value);
        $expression->setIsChecked($node->isChecked());
        $expression->setIsEscaped($node->isEscaped());

        return $expression;
    }
}
