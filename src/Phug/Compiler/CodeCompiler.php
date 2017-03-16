<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\CodeElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\CodeNode;
use Phug\Parser\NodeInterface;

class CodeCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof CodeNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to code compiler.'
            );
        }

        return new CodeElement($this->getTextChildren($node));
    }
}
