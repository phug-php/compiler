<?php

namespace Phug\Compiler;

use Phug\AbstractStatementNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\EachNode;
use Phug\Parser\NodeInterface;

class EachCompiler extends AbstractStatementNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof EachNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to each compiler.'
            );
        }

        /**
         * @var EachNode $node
         */
        $subject = $node->getSubject();

        return $this->wrapStatement($node, 'foreach', $subject);
    }
}
