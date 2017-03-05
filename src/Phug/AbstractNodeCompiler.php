<?php

namespace Phug;

use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\CodeNode;
use Phug\Parser\NodeInterface;

abstract class AbstractNodeCompiler implements NodeCompilerInterface
{
    /**
     * @var CompilerInterface
     */
    private $compiler;

    public function __construct(CompilerInterface $compiler)
    {
        $this->compiler = $compiler;
    }

    public function getCompiler()
    {
        return $this->compiler;
    }

    public function compileNodeChildren(NodeInterface $node, ElementInterface $element)
    {
        $children = $node->getChildren();
        array_walk($children, function (NodeInterface $childNode) use ($element) {
            $childElement = $this->getCompiler()->compileNode($childNode, $element);
            if ($childElement) {
                $element->appendChild($childElement);
            }
        });
    }
}
