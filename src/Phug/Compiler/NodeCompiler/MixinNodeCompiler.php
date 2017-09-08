<?php

namespace Phug\Compiler\NodeCompiler;

use Phug\Compiler\AbstractNodeCompiler;
use Phug\Formatter\Element\AttributeElement;
use Phug\Formatter\Element\ExpressionElement;
use Phug\Formatter\Element\MixinElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\AttributeNode;
use Phug\Parser\Node\ExpressionNode;
use Phug\Parser\Node\MixinNode;
use Phug\Parser\NodeInterface;

class MixinNodeCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof MixinNode)) {
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to mixin compiler.',
                $node
            );
        }

        $compiler = $this->getCompiler();

        /** @var MixinNode $node */
        $name = strval($node->getName());
        $mixin = new MixinElement();
        $mixin->setName($name);

        foreach ($node->getAttributes() as $attribute) {
            /* @var AttributeNode $attribute */
            /* @var AttributeElement $attributeElement */
            $attributeElement = $compiler->compileNode($attribute, $parent);
            if (is_null($attribute->getValue())) {
                $attributeElement->setValue(new ExpressionElement('null', $attribute));
            }
            $mixin->getAttributes()->attach($attributeElement);
        }

        $this->compileNodeChildren($node, $mixin);

        return $mixin;
    }
}
