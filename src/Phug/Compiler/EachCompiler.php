<?php

namespace Phug\Compiler;

use Phug\AbstractStatementNodeCompiler;
use Phug\Formatter\Element\CodeElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\CommentNode;
use Phug\Parser\Node\ConditionalNode;
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

        /** @var CodeElement $loop */
        $loop = $this->wrapStatement($node, 'foreach', $subject);
        $next = $node->getNextSibling();

        while ($next && $next instanceof CommentNode) {
            $next = $next->getNextSibling();
        }

        if ($next instanceof ConditionalNode && $next->getName() === 'else') {
            $next->setName('if');
            $next->setSubject('$__pug_temp_empty');
            $loop->setValue('$__pug_temp_empty = true; '.$loop->getValue());
            $loop->prependChild(new CodeElement($next, '$__pug_temp_empty = false'));
        }

        return $loop;
    }

    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof EachNode)) {
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to each compiler.',
                $node
            );
        }

        /** @var EachNode $node */
        $subject = $node->getSubject();
        $key = $node->getKey();
        $item = $node->getItem();

        return $this->compileLoop($node, $subject, $key, $item);
    }
}
