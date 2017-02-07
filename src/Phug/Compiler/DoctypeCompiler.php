<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\DoctypeElement;
use Phug\Parser\Node\DoctypeNode;
use Phug\Parser\NodeInterface;

class DoctypeCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof DoctypeNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to doctype compiler.'
            );
        }

        return new DoctypeElement($node->getName());
    }
}
