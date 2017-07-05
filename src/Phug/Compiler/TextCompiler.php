<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\TextElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\TextNode;
use Phug\Parser\NodeInterface;

class TextCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof TextNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to text compiler.'
            );
        }

        $text = new TextElement($node, $node->getValue());
        $text->setIsEscaped($node->isEscaped());

        return $text;
    }
}
