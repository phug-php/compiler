<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\AssignmentElement;
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

        $compiler = $this->getCompiler();

        /**
         * @var ElementNode $node
         */
        $name = $node->getName() ?: $compiler->getOption('default_tag');

        $attributes = new SplObjectStorage();
        foreach ($node->getAttributes() as $attribute) {
            $attributes->attach($compiler->compileNode($attribute, $parent));
        }
        $markup = new MarkupElement($name, $attributes);
        foreach ($node->getAssignments() as $assignment) {
            $compiledAssignment = $compiler->compileNode($assignment, $parent);
            if ($compiledAssignment instanceof AssignmentElement) {
                $markup->addAssignment($compiledAssignment);
            }
        }

        $this->compileNodeChildren($node, $markup);

        return $markup;
    }
}
