<?php

namespace Phug\Compiler;

use Phug\Ast\NodeInterface;
use Phug\CompilerInterface;
use Phug\Formatter\AbstractElement;
use Phug\Util\UnorderedArguments;

class Block extends AbstractElement
{
    /**
     * @var array[CompilerInterface]
     */
    protected $compilers;

    /**
     * @var string
     */
    protected $name;

    public function __construct()
    {
        $arguments = new UnorderedArguments(func_get_args());

        $compiler = $arguments->required(CompilerInterface::class);
        $name = $arguments->optional('string') ?: '';
        $blocks = &$compiler->getBlocksByName($name);
        $blocks[] = $this;
        $this->compilers = [$compiler];
        $this->name = $name;
        $parent = $arguments->optional(NodeInterface::class);
        $children = $arguments->optional('array');

        $arguments->noMoreDefinedArguments();

        parent::__construct($parent, $children);
    }

    /**
     * Link another compiler.
     *
     * @param CompilerInterface $compiler
     */
    public function addCompiler(CompilerInterface $compiler)
    {
        if (!in_array($compiler, $this->compilers)) {
            $blocks = &$compiler->getBlocksByName($this->name);
            $blocks[] = $this;
            $this->compilers[] = $compiler;
        }

        return $this;
    }

    public function proceedChildren(array $newChildren, $mode)
    {
        $offset = 0;
        $length = 0;
        $children = $this->getChildren();

        if ($mode === 'replace') {
            $length = count($children);
        } elseif ($mode === 'append') {
            $offset = count($children);
        }

        array_splice($children, $offset, $length, $newChildren);

        return $this->setChildren($children);
    }

    public function __clone()
    {
        parent::__clone();

        foreach ($this->compilers as $compiler) {
            $blocks = &$compiler->getBlocksByName($this->name);
            $blocks[] = $this;
        }
    }
}
