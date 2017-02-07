<?php

namespace Phug;

use Phug\Parser\NodeInterface;

interface NodeCompilerInterface
{
    public function compileNode(NodeInterface $node);

    public function getCompiler();
}
