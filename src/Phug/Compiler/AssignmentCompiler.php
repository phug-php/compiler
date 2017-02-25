<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\AssignmentElement;
use Phug\Parser\Node\AssignmentNode;
use Phug\Parser\NodeInterface;

class AssignmentCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
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
        $attributes = $node->getAttributes();

        return new AssignmentElement($name, $attributes);
    }
}
