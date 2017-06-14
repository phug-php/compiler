<?php

namespace Phug\Compiler;

use Phug\AbstractStatementNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\EachNode;
use Phug\Parser\NodeInterface;

class EachCompiler extends AbstractStatementNodeCompiler
{
    protected function compileLoop(NodeInterface $node, $items, $key, $item)
    {
        $subject = $this->getCompiler()->getFormatter()->formatCode($items).' as ';
        if ($key) {
            $subject .= '$'.$key.' => ';
        }
        $subject .= '$'.$item;

        return $this->wrapStatement($node, 'foreach', $subject);
    }

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
        $key = $node->getKey();
        $item = $node->getItem();

        return $this->compileLoop($node, $subject, $key, $item);
    }
}
