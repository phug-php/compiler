<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\AttributeElement;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\NodeInterface;
use SplObjectStorage;

class ElementCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof ElementNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to element compiler.'
            );
        }

        $attributes = new SplObjectStorage();
        foreach ($node->getAttributes() as $attribute) {
            $attributes->attach($this->getCompiler()->compileNode($attribute));
        }
        $markup = new MarkupElement($node->getName(), $attributes);

        $this->compileNodeChildren($node, $markup);

        return $markup;
    }
}
