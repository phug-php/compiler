<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\BlockNode;
use Phug\Parser\NodeInterface;

class BlockCompiler extends AbstractNodeCompiler
{
    protected function compileNamedBlock($name, BlockNode $node)
    {
        $compiler = $this->getCompiler();
        $layout = $compiler->getLayout();

        if ($layout) {
            $blocks = &$layout->getCompiler()->getBlocksByName($name);
            array_walk($blocks, function (Block $block) use ($node) {
                $block->proceedNode($node);
            });

            return null;
        }

        $block = new Block();
        $blocks = &$compiler->getBlocksByName($name);
        $blocks[] = $block;

        return $block->proceedNode($node);
    }

    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof BlockNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to block compiler.'
            );
        }

        /**
         * @var BlockNode $node
         */
        $name = $node->getName();

        if (!$name) {
            $compiler = $this->getCompiler();
            $block = $compiler->getMixinBlock($name);
            $block->import($node);

            return $block;
        }

        return $this->compileNamedBlock($name, $node);
    }
}
