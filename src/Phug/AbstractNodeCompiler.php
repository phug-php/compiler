<?php

namespace Phug;

use Phug\Formatter\ElementInterface;
use Phug\Parser\NodeInterface;

abstract class AbstractNodeCompiler implements NodeCompilerInterface
{
    private $compiler;

    public function __construct(CompilerInterface $compiler)
    {
        $this->compiler = $compiler;
    }

    public function compileNodeChildren(NodeInterface $node, ElementInterface $element)
    {
        foreach ($node->getChildren() ?: [] as $child) {
            $element->appendChild($this->compiler->compileNode($child));
        }
    }
}
