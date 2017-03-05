<?php

namespace Phug\Compiler;

use Phug\AbstractStatementNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\DoNode;
use Phug\Parser\Node\WhileNode;
use Phug\Parser\NodeInterface;

class WhileCompiler extends AbstractStatementNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof WhileNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to while compiler.'
            );
        }

        /**
         * @var WhileNode $node
         */
        $subject = $node->getSubject();
        $linkedToDoStatement = $node->getPreviousSibling() instanceof DoNode;
        if ($linkedToDoStatement && $node->hasChildren()) {
            throw new CompilerException(
                'While statement cannot have children and come after a do statement.'
            );
        }
        $whileEnd = $linkedToDoStatement ? ';' : ' {}';

        return $this->wrapStatement($node, 'while', $subject, $whileEnd);
    }
}
