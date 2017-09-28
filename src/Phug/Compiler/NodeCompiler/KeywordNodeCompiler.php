<?php

namespace Phug\Compiler\NodeCompiler;

use Phug\Compiler\AbstractNodeCompiler;
use Phug\Formatter\Element\KeywordElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\KeywordNode;
use Phug\Parser\NodeInterface;

class KeywordNodeCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof KeywordNode)) {
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to keyword compiler.',
                $node
            );
        }

        $keyword = new KeywordElement(
            $node->getName(),
            $node->getValue(),
            $node,
            $parent
        );

        $this->compileNodeChildren($node, $keyword);

        return $keyword;
    }
}
