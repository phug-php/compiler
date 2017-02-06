<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\ImportNode;
use Phug\Parser\NodeInterface;

class ImportCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof ImportNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to import compiler.'
            );
        }

        return new MarkupElement('to-do-import');
    }
}
