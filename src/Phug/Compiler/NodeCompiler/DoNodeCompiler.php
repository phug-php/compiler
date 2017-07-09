<?php

namespace Phug\Compiler\NodeCompiler;

use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\DoNode;
use Phug\Parser\NodeInterface;

class DoNodeCompiler extends AbstractStatementNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof DoNode)) {
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to do compiler.',
                $node
            );
        }

        return $this->wrapStatement($node, 'do');
    }
}
