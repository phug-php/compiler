<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\CodeElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\CodeNode;
use Phug\Parser\Node\CommentNode;
use Phug\Parser\Node\TextNode;
use Phug\Parser\NodeInterface;

class CodeCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof CodeNode)) {
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to code compiler.',
                $node
            );
        }

        $children = array_filter($node->getChildren(), function (NodeInterface $node) {
            return !($node instanceof CommentNode);
        });

        $texts = array_filter($children, function (NodeInterface $node) {
            return $node instanceof TextNode;
        });

        if (count($texts) === count($children)) {
            return new CodeElement($node, $this->getTextChildren($node));
        }

        $code = new CodeElement($node);
        if ($children[0] instanceof TextNode) {
            $code->setValue($children[0]->getValue());
            $children = array_slice($children, 1);
        }
        $code->setChildren($this->getCompiledNodeList($children, $parent));

        return $code;
    }
}
