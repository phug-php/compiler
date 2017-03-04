<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\CodeElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\CaseNode;
use Phug\Parser\NodeInterface;

class CaseCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof CaseNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to case compiler.'
            );
        }

        /**
         * @var CaseNode $node
         */
        $value = $node->getSubject();

        $code = new CodeElement('switch ('.$value.')');

        $this->compileNodeChildren($node, $code);

        return $code;
    }
}
