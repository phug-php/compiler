<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\AssignementNode;
use Phug\Parser\NodeInterface;

class AssignementCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof AssignementNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to assignement compiler.'
            );
        }

        return new MarkupElement('to-do-assignement');
    }
}
