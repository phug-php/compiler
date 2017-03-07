<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\MixinNode;
use Phug\Parser\NodeInterface;

class MixinCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof MixinNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to mixin compiler.'
            );
        }

        /**
         * @var MixinNode $node
         */
        $name = $node->getName();
        $compiler = $this->getCompiler();
        $mixins = $compiler->getMixins();
        $mixins->attach($node);

        return null;
    }
}
