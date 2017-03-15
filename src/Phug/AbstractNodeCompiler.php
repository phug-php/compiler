<?php

namespace Phug;

use Phug\Ast\NodeInterface;
use Phug\Formatter\Element\CodeElement;
use Phug\Formatter\Element\VariableElement;
use Phug\Formatter\ElementInterface;

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

    public function getCompiledChildren(NodeInterface $node, ElementInterface $element = null)
    {
        return array_filter(array_map(function (NodeInterface $childNode) use ($element) {
            return $this->getCompiler()->compileNode($childNode, $element);
        }, $node->getChildren()));
    }

    public function compileNodeChildren(NodeInterface $node, ElementInterface $element = null)
    {
        $children = $node->getChildren();
        array_walk($children, function (NodeInterface $childNode) use ($element) {
            $childElement = $this->getCompiler()->compileNode($childNode, $element);
            if ($childElement) {
                $element->appendChild($childElement);
            }
        });
    }

    public function createVariable($name, $value)
    {
        $variable = new CodeElement('$'.$name);

        return new VariableElement($variable, $value);
    }
}
