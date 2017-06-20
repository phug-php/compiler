<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\DoctypeElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\DoctypeNode;
use Phug\Parser\NodeInterface;

class DoctypeCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof DoctypeNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to doctype compiler.'
            );
        }

        $name = $node->getName() ?: $this->getCompiler()->getOption('default_doctype');

        return new DoctypeElement($name);
    }
}
