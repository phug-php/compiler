<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\MixinCallNode;
use Phug\Parser\NodeInterface;

class MixinCallCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof MixinCallNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to mixin call compiler.'
            );
        }

        /**
         * @var MixinCallNode $node
         */
        $name = $node->getName();
        $compiler = $this->getCompiler();
        $mixins = $compiler->getMixins();
        $declaration = $mixins->findFirstByName($name);
        if (!$declaration) {
            throw new CompilerException(
                'Unknown '.$name.' mixin called.'
            );
        }
        $block = $compiler->getMixinBlock($name);
        $block->proceedNodeChildren($node, 'replace');

        return null;
    }
}
