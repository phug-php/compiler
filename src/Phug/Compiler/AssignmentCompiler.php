<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\AssignmentElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\AssignmentNode;
use Phug\Parser\Node\AttributeNode;
use Phug\Parser\NodeInterface;
use SplObjectStorage;

class AssignmentCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof AssignmentNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to assignment compiler.'
            );
        }

        /**
         * @var AssignmentNode $node
         */
        $name = $node->getName();
        $attributes = new SplObjectStorage();
        $input = iterator_to_array($node->getAttributes());
        array_walk($input, function (AttributeNode $attribute) use ($attributes, $parent) {
            $attributes->attach($this->getCompiler()->compileNode($attribute, $parent));
        });

        return new AssignmentElement($node, $name, $attributes);
    }
}
