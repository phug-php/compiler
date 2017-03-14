<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\BlockNode;
use Phug\Parser\Node\MixinNode;
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
            $block = new Block();
            $block->import($node);
            $declaration = null;
            while ($node->hasParent() && !($node instanceof MixinNode)) {
                $node = $node->getParent();
            }
            if ($node instanceof MixinNode) {
                $declaration = $node;
            }
            if (!$declaration) {
                throw new CompilerException(
                    'Anonymous block should only be in a mixin declaration.'
                );
            }
            $compiler->getMixinBlocks()->attach($declaration, $block);

            return $block;
        }

        return $this->compileNamedBlock($name, $node);
    }
}
