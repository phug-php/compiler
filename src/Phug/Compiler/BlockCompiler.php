<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\BlockNode;
use Phug\Parser\NodeInterface;

class BlockCompiler extends AbstractNodeCompiler
{
    protected function compileNamedBlock($name, BlockNode $node)
    {
        $compiler = $this->getCompiler();
        $layout = $compiler->getLayout();

        if ($layout) {
            var_dump($layout->getCompiler()->getBlocks(), $compiler->getBlocks());
            exit;
            $blocks = &$layout->getCompiler()->getBlocksByName($name);
            foreach ($blocks as $block) {
                $block->proceedNode($node);
            }

            return;
        }

        $block = new Block();
        $blocks = &$compiler->getBlocksByName($name);
        $blocks[] = $block;

        return $block->proceedNode($node);
    }

    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof BlockNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to block compiler.'
            );
        }

        $name = $node->getName();

        if (!$name) {
            return new MarkupElement('to-do-anonymous-block');
        }

        return $this->compileNamedBlock($name, $node);
    }
}
