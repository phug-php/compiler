<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\MixinNode;
use Phug\Parser\NodeInterface;

class MixinCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof MixinNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to mixin compiler.'
            );
        }

        return new MarkupElement('to-do-mixin');
    }
}
