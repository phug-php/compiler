<?php

namespace Phug\Test;

use Phug\Compiler;
use Phug\Parser\NodeInterface;

class TestCompiler extends Compiler
{
    public function compileNode(NodeInterface $node)
    {
        return 'foo';
    }
}
