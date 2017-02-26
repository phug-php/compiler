<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\TextElement;
use Phug\Parser\Node\TextNode;
use Phug\Parser\NodeInterface;

class TextCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof TextNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to text compiler.'
            );
        }

        $text = new TextElement($node->getValue());
        $text->setIsEscaped($node->isEscaped());

        return $text;
    }
}
