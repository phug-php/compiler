<?php

namespace Phug;

use Phug\Compiler\Block;
use Phug\Compiler\Layout;
use Phug\Formatter\ElementInterface;
use Phug\Parser\NodeInterface;
use Phug\Util\AssociativeStorage;
use Phug\Util\OptionInterface;
use SplObjectStorage;

interface CompilerInterface extends OptionInterface
{
    /**
     * @return Parser
     */
    public function getParser();

    /**
     * @return Formatter
     */
    public function getFormatter();

    /**
     * @return Layout
     */
    public function getLayout();

    /**
     * @return AssociativeStorage
     */
    public function getMixins();

    /**
     * @param Layout $layout
     *
     * @return mixed
     */
    public function setLayout(Layout $layout);

    /**
     * @param string                $className
     * @param NodeCompilerInterface $handler
     *
     * @return null|ElementInterface
     */
    public function setNodeCompiler($className, $handler);

    /**
     * @param string $name
     *
     * @return array
     */
    public function &getBlocksByName($name);

    /**
     * @param string $mixinName
     *
     * @return Block
     */
    public function &getMixinBlock($mixinName);

    /**
     * @return SplObjectStorage
     */
    public function getMixinBlocks();


    /**
     * @return array
     */
    public function getBlocks();

    /**
     * @param Block $block
     * @param array $nodes
     */
    public function replaceBlock(Block $block, array $nodes);

    /**
     * @throws CompilerException
     *
     * @return $this
     */
    public function compileBlocks();

    /**
     * @param NodeInterface         $node
     * @param ElementInterface|null $parent
     *
     * @return null|ElementInterface
     */
    public function compileNode(NodeInterface $node, ElementInterface $parent = null);

    /**
     * @param string $pugInput
     * @param string $fileName
     *
     * @return string
     */
    public function compile($pugInput, $fileName = null);

    /**
     * @param string $fileName
     *
     * @return string
     */
    public function compileFile($fileName);

    /**
     * @param string $pugInput
     * @param string $fileName
     *
     * @return null|ElementInterface
     */
    public function compileIntoElement($pugInput, $fileName = null);

    /**
     * @param string $fileName
     *
     * @return null|ElementInterface
     */
    public function compileFileIntoElement($fileName);

    /**
     * @return null|string
     */
    public function getFileName();
}
