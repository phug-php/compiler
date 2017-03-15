<?php

namespace Phug;

use Phug\Ast\NodeInterface;
use Phug\Compiler\Block;
use Phug\Formatter\ElementInterface;
use Phug\Parser\NodeInterface as ParserNodeInterface;

interface NodeCompilerInterface
{
    /**
     * @return array
     */
    public function getCompiledChildren(NodeInterface $node, ElementInterface $parent = null);

    public function compileNodeChildren(NodeInterface $node, ElementInterface $element = null);

    /**
     * @return ElementInterface|Block|null
     */
    public function compileNode(ParserNodeInterface $node, ElementInterface $parent = null);

    /**
     * @return CompilerInterface
     */
    public function getCompiler();
}
