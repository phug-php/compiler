<?php

namespace Phug\Compiler\NodeCompiler;

use Phug\Compiler\AbstractNodeCompiler;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\AssignmentListNode;
use Phug\Parser\NodeInterface;

class AssignmentListNodeCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof AssignmentListNode)) {
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to assignment list compiler.',
                $node
            );
        }

        // @codeCoverageIgnoreStart
        return null;
        // @codeCoverageIgnoreEnd
    }
}
