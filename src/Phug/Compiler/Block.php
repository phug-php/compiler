<?php

namespace Phug\Compiler;

use Phug\Parser\Node\BlockNode;

class Block
{
    /**
     * @var array
     */
    private $children = [];

    public function proceedNode(BlockNode $node)
    {
        $offset = 0;
        $length = 0;

        if ($node->getMode() === 'replace') {
            $length = count($this->children);
        } elseif ($node->getMode() === 'append') {
            $offset = count($this->children);
        }

        array_splice($this->children, $offset, $length, $node->getChildren());

        return $this;
    }

    public function getChildren()
    {
        return $this->children;
    }
}
