<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\AttributeElement;
use Phug\Formatter\Element\ExpressionElement;
use Phug\Formatter\Element\TextElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\AttributeNode;
use Phug\Parser\NodeInterface;

class AttributeCompiler extends AbstractNodeCompiler
{
    protected function compileName(AttributeNode $node)
    {
        $name = $node->getName();

        if ($node->hasStaticMember('name')) {
            return strval(eval('return '.$name.';'));
        }

        return $name;
    }

    protected function compileValue(AttributeNode $node)
    {
        $value = $node->getValue();

        if ($node->hasStaticValue()) {
            // eval is safe here since pass to it only one valid number or constant string.
            $value = strval(eval('return '.$value.';'));
            $value = new TextElement($node, $value);
            $value->setIsEscaped($node->isEscaped());

            return $value;
        }

        if (is_null($value)) {
            $value = 'true';
        }

        if (is_string($value)) {
            $value = new ExpressionElement($node, $value);
        }

        if (!($value instanceof ExpressionElement)) {
            $this->getCompiler()->throwException(
                'Attribute value can only be a string, a boolean or an expression, '.
                get_class($value).' given.',
                $node
            );
        }

        $value->setIsEscaped($node->isEscaped());
        $value->setIsChecked($node->isChecked());

        return $value;
    }

    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof AttributeNode)) {
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to attribute compiler.',
                $node
            );
        }

        /**
         * @var AttributeNode $node
         */
        $name = $this->compileName($node);
        $value = $this->compileValue($node);

        return new AttributeElement($name, $value, $node);
    }
}
