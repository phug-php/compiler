<?php

namespace Phug\Compiler;

use Phug\AbstractStatementNodeCompiler;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\CaseNode;
use Phug\Parser\NodeInterface;

class CaseCompiler extends AbstractStatementNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof CaseNode)) {
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to case compiler.',
                $node
            );
        }

        /**
         * @var CaseNode $node
         */
        $subject = $node->getSubject();

        return $this->wrapStatement($node, 'switch', $subject);
    }
}
