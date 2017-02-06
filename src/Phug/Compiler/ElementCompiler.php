<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\NodeInterface;

class ElementCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof ElementNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to element compiler.'
            );
        }

        $markup = new MarkupElement();
        $markup->setName($node->getName());
        $markup->getAttributes()->addAll($node->getAttributes());

        $this->compileNodeChildren($node, $markup);

        return $markup;
    }
}
