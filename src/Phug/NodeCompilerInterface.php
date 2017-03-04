<?php

namespace Phug;

use Phug\Formatter\ElementInterface;
use Phug\Parser\NodeInterface;

interface NodeCompilerInterface
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null);

    public function getCompiler();
}
