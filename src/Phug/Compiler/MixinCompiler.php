<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\MixinNode;
use Phug\Parser\NodeInterface;

class MixinCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof MixinNode)) {
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to mixin compiler.',
                $node
            );
        }

        $node->mixinConstructor = function () use ($node, $parent) {
            $node->setChildren($this->getCompiledChildren($node, $parent));
        };
        $this->getCompiler()->getMixins()->attach($node);

        return null;
    }
}
