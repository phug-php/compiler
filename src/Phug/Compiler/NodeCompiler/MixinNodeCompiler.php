<?php

namespace Phug\Compiler\NodeCompiler;

use Phug\Compiler\AbstractNodeCompiler;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\MixinNode;
use Phug\Parser\NodeInterface;

class MixinNodeCompiler extends AbstractNodeCompiler
{
    protected function containsMixinCall(NodeInterface $node)
    {
        foreach ($node->getChildren() as $child) {
            if ($child instanceof MixinNode || (
                $child instanceof NodeInterface &&
                $this->containsMixinCall($child)
            )) {
                return true;
            }
        }

        return false;
    }

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
        $compiler = $this->getCompiler();
        if ($this->containsMixinCall($node)) {
            $compiler->enableDynamicMixins();
        }
        $compiler->getMixins()->attach($node);

        return null;
    }
}
