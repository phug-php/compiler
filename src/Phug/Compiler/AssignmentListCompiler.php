<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\AssignmentListNode;
use Phug\Parser\NodeInterface;

class AssignmentListCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof AssignmentListNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to assignment list compiler.'
            );
        }

        // @codeCoverageIgnoreStart
        /**
         * @var AssignmentListNode $assignmentList
         */
        $assignmentList = $node;

        return new MarkupElement('to-do-assignement-list');
        // @codeCoverageIgnoreEnd
    }
}
