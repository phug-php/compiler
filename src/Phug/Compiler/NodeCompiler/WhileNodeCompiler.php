<?php

namespace Phug\Compiler\NodeCompiler;

use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\DoNode;
use Phug\Parser\Node\WhileNode;
use Phug\Parser\NodeInterface;

class WhileNodeCompiler extends AbstractStatementNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof WhileNode)) {
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to while compiler.',
                $node
            );
        }

        /**
         * @var WhileNode $node
         */
        $subject = $node->getSubject();
        $linkedToDoStatement = $node->getPreviousSibling() instanceof DoNode;
        if ($linkedToDoStatement && $node->hasChildren()) {
            $this->getCompiler()->throwException(
                'While statement cannot have children and come after a do statement.',
                $node
            );
        }
        $whileEnd = $linkedToDoStatement ? ';' : ' {}';

        return $this->wrapStatement($node, 'while', $subject, $whileEnd);
    }
}
