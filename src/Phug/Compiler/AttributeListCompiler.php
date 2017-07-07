<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\AttributeListNode;
use Phug\Parser\NodeInterface;

class AttributeListCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof AttributeListNode)) {
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to attribute list compiler.',
                $node
            );
        }

        // @codeCoverageIgnoreStart
        return null;
        // @codeCoverageIgnoreEnd
    }
}
