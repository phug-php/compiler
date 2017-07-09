<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\Formatter\Element\TextElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\TextNode;
use Phug\Parser\NodeInterface;

class TextCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof TextNode)) {
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to text compiler.',
                $node
            );
        }

        $text = new TextElement($node, $node->getValue());
        $text->setIsEscaped($node->isEscaped());

        return $text;
    }
}
