<?php

namespace Phug\Compiler;

use Phug\AbstractStatementNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\ConditionalNode;
use Phug\Parser\NodeInterface;

class ConditionalCompiler extends AbstractStatementNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof ConditionalNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to conditional compiler.'
            );
        }

        /**
         * @var ConditionalNode $node
         */
        $subject = $node->getSubject();
        $name = $node->getName();
        if ($name === 'unless') {
            $name = 'if';
            $subject = '!('.$subject.')';
        }

        return $this->wrapStatement($node, $name, $subject);
    }
}
