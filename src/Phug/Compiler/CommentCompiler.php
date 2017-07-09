<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\Formatter\Element\CommentElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\CommentNode;
use Phug\Parser\NodeInterface;

class CommentCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof CommentNode)) {
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to comment compiler.',
                $node
            );
        }

        /** @var CommentNode $node */
        if (!$node->isVisible()) {
            return null;
        }

        $comment = $this->getTextChildren($node);

        return new CommentElement($node, $comment);
    }
}
