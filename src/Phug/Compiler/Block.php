<?php

namespace Phug\Compiler;

use Phug\Ast\NodeInterface;
use Phug\Formatter\AbstractElement;
use Phug\Parser\Node\BlockNode;

class Block extends AbstractElement
{
    public function proceedChildren(array $newChildren, $mode)
    {
        $offset = 0;
        $length = 0;
        $children = $this->getChildren();

        if ($mode === 'replace') {
            $length = count($children);
        } elseif ($mode === 'append') {
            $offset = count($children);
        }

        array_splice($children, $offset, $length, $newChildren);

        return $this->setChildren($children);
    }
}
