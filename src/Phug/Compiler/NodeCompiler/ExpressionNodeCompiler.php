<?php

namespace Phug\Compiler\NodeCompiler;

use Phug\Compiler\AbstractNodeCompiler;
use Phug\Formatter\Element\ExpressionElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\ExpressionNode;
use Phug\Parser\NodeInterface;

class ExpressionNodeCompiler extends AbstractNodeCompiler
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
        $expression = new ExpressionElement($value, $node);
        $expression->setIsChecked($node->isChecked());
        $expression->setIsEscaped($node->isEscaped());

        return $expression;
    }
}
