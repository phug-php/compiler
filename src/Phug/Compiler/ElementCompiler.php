<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\NodeInterface;
use SplObjectStorage;

class ElementCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof ElementNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to element compiler.'
            );
        }

        /**
         * @var ElementNode $element
         */
        $element = $node;

        $attributes = new SplObjectStorage();
        foreach ($element->getAttributes() as $attribute) {
            $attributes->attach($this->getCompiler()->compileNode($attribute));
        }
        $markup = new MarkupElement($element->getName(), $attributes);
        foreach ($element->getAssignments() as $assignment) {
            $markup->addAssignment($this->getCompiler()->compileNode($assignment));
        }

        $this->compileNodeChildren($element, $markup);

        return $markup;
    }
}
