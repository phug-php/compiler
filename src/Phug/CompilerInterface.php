<?php

namespace Phug;

use Phug\Parser\NodeInterface;

interface CompilerInterface
{
    public function getParser();

    public function getFormatter();

    public function setNodeCompiler($className, $handler);

    public function compileNode(NodeInterface $node);

    public function compile($pugInput);
}
