<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\CommentNode;
use Phug\Parser\NodeInterface;

class CommentCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof CommentNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to comment compiler.'
            );
        }

        return new MarkupElement('to-do-comment');
    }
}
