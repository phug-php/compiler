<?php

namespace Phug\Compiler;

use Phug\Formatter\AbstractElement;

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
