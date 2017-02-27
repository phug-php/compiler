<?php

namespace Phug;

use Phug\Parser\NodeInterface;
use Phug\Util\OptionInterface;

interface CompilerInterface extends OptionInterface
{
    public function getParser();

    public function getFormatter();

    public function setNodeCompiler($className, $handler);

    public function &getNamedBlock($name);

    public function compileNode(NodeInterface $node);

    public function compile($pugInput);

    public function getFileName();
}
