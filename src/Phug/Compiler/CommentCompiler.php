<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\TextElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\CommentNode;
use Phug\Parser\Node\TextNode;
use Phug\Parser\NodeInterface;

class CommentCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof CommentNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to comment compiler.'
            );
        }

        if (!$node->isVisible()) {
            return null;
        }

        $comment = implode("\n", array_map(function (TextNode $text) {
            return $text->getValue();
        }, $node->getChildren()));

        return new TextElement('<!-- '.$comment.' -->');
    }
}
