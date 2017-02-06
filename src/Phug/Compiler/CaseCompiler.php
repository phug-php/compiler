<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\CaseNode;
use Phug\Parser\NodeInterface;

class CaseCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof CaseNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to case compiler.'
            );
        }

        return new MarkupElement('to-do-case');
    }
}
