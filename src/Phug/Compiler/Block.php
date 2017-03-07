<?php

namespace Phug\Compiler;

use Phug\Ast\NodeInterface;
use Phug\Formatter\AbstractElement;
use Phug\Parser\Node\BlockNode;

class Block extends AbstractElement
{
    /**
     * @var array
     */
    private $children = [];

    public function import(NodeInterface $node)
    {
        $this->children = $node->getChildren();
    }

    public function proceedNodeChildren(NodeInterface $node, $mode)
    {
        $offset = 0;
        $length = 0;

        if ($mode === 'replace') {
            $length = count($this->children);
        } elseif ($mode === 'append') {
            $offset = count($this->children);
        }

        array_splice($this->children, $offset, $length, $node->getChildren());

        return $this;
    }

    public function proceedNode(BlockNode $node)
    {
        return $this->proceedNodeChildren($node, $node->getMode());
    }

    public function getChildren()
    {
        return $this->children;
    }
}
