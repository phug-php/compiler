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
    protected function compileAnonymousBlock(BlockNode $node, ElementInterface $parent)
    {
        $block = new Block($parent);
        $block->setChildren($this->getCompiledChildren($node, $parent));
        $mixin = $node;
        while ($mixin->hasParent() && !($mixin instanceof MixinNode)) {
            $mixin = $mixin->getParent();
        }
        if (!($mixin instanceof MixinNode)) {
            if ($importNode = $this->getCompiler()->getImportNode()) {
                $this->compileNodeChildren($importNode, $parent);
            }

            return null;
        }

        return $block;
    }

    protected function hasBlockParent(BlockNode $node)
    {
        for ($blockParent = $node->getParent(); $blockParent; $blockParent = $blockParent->getParent()) {
            if ($blockParent instanceof BlockNode) {
                return true;
            }
        }

        return false;
    }

    protected function compileNamedBlock($name, BlockNode $node, ElementInterface $parent)
    {
        $compiler = $this->getCompiler();
        $layout = $compiler->getLayout();

        if ($layout && !$this->hasBlockParent($node)) {
            $blocks = &$layout->getCompiler()->getBlocksByName($name);
            array_walk($blocks, function (Block $block) use ($node) {
                $block->proceedChildren(
                    $this->getCompiledChildren($node, $block->getParent()),
                    $node->getMode()
                );
            });

            return null;
        }

        $block = new Block($parent);
        $blocks = &$compiler->getBlocksByName($name);
        $blocks[] = $block;

        return $block->proceedChildren(
            $this->getCompiledChildren($node, $parent),
            $node->getMode()
        );
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

        return $name
            ? $this->compileNamedBlock($name, $node, $parent)
            : $this->compileAnonymousBlock($node, $parent);
    }
}
