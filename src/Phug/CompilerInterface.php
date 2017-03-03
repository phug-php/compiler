<?php

namespace Phug;

use Phug\Parser\NodeInterface;
use Phug\Util\OptionInterface;

interface CompilerInterface extends OptionInterface
{
    public function getParser();

    public function getFormatter();

    public function setNodeCompiler($className, $handler);

    public function &getBlocksByName($name);

    public function getBlocks();

    public function compileNode(NodeInterface $node);

    public function compile($pugInput, $fileName = null);

    public function compileFile($fileName);

    public function compileIntoElement($pugInput, $fileName = null);

    public function compileFileIntoElement($fileName);

    public function getFileName();
}
