<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\AttributeElement;
use Phug\Formatter\Element\ExpressionElement;
use Phug\Formatter\Element\TextElement;
use Phug\Parser\Node\AttributeNode;
use Phug\Parser\NodeInterface;

class AttributeCompiler extends AbstractNodeCompiler
{
    protected function compileValue(AttributeNode $node)
    {
        $value = $node->getValue();

        if (is_string($value)) {
            $tokens = token_get_all('<?php '.$value);
            if (
                count($tokens) === 2 &&
                is_array($tokens[1]) &&
                in_array($tokens[1][0], [T_CONSTANT_ENCAPSED_STRING, T_DNUMBER, T_LNUMBER])
            ) {
                // eval is safe here since pass to it only one valid number or constant string.
                $value = strval(eval('return '.$value.';'));
                $value = new TextElement($value);
                $value->setIsEscaped($node->isEscaped());

                return $value;
            }

            $value = new ExpressionElement($value);
        }

        if (!($value instanceof ExpressionElement)) {
            throw new CompilerException(
                'Attribute value can only be a string or an expression, '.
                get_class($value).' given.'
            );
        }

        $value->setIsEscaped($node->isEscaped());
        $value->setIsChecked($node->isChecked());

        return $value;
    }

    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof AttributeNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to attribute compiler.'
            );
        }
        /**
         * @var AttributeNode $node
         */

        return new AttributeElement($node->getName(), $this->compileValue($node));
    }
}
