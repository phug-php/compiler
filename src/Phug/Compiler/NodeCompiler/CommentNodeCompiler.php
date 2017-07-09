<?php

namespace Phug\Compiler\NodeCompiler;

use Phug\Compiler\AbstractNodeCompiler;
use Phug\Formatter\Element\CommentElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\CommentNode;
use Phug\Parser\NodeInterface;

class CommentNodeCompiler extends AbstractNodeCompiler
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

        return new CommentElement($comment, $node);
    }
}