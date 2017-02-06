<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\AssignementListNode;
use Phug\Parser\NodeInterface;

class AssignementListCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof AssignementListNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to assignement list compiler.'
            );
        }

        return new MarkupElement('to-do-assignement-list');
    }
}
