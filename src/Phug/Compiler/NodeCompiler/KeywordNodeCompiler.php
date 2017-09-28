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

        $compiler = $this->getCompiler();

        $keyword = new KeywordElement(
            $node->getName(),
            $node->getValue(),
            $node,
            $parent
        );

        $this->compileNodeChildren($node, $keyword);

        $outer = $node->getOuterNode();
        if ($outer) {
            $outerMarkup = $compiler->compileNode($outer);
            $outerMarkup->appendChild($keyword);

            return $outerMarkup;
        }

        return $keyword;
    }
}
