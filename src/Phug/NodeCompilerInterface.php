<?php

namespace Phug;

use Phug\Compiler\Block;
use Phug\Formatter\ElementInterface;
use Phug\Parser\NodeInterface;

interface NodeCompilerInterface
{
    /**
     * @return ElementInterface|Block|null
     */
    public function compileNode(NodeInterface $node, ElementInterface $parent = null);

    /**
     * @return CompilerInterface
     */
    public function getCompiler();
}
