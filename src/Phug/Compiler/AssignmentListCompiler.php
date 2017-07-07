<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\AssignmentListNode;
use Phug\Parser\NodeInterface;

class AssignmentListCompiler extends AbstractNodeCompiler
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
