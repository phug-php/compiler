<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\MixinCallNode;
use Phug\Parser\NodeInterface;

class MixinCallCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof MixinCallNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to mixin call compiler.'
            );
        }

        return new MarkupElement('to-do-mixin-call');
    }
}
