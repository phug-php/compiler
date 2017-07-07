<?php

namespace Phug;

use Phug\Compiler\Block;
use Phug\Compiler\Layout;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\MixinNode;
use Phug\Parser\NodeInterface;
use Phug\Util\AssociativeStorage;
use Phug\Util\OptionInterface;

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
     * @return array
     */
    public function getBlocks();

    /**
     * @param Block $block
     * @param array $children
     */
    public function replaceBlock(Block $block, array $children = null);

    /**
     * @throws CompilerException
     *
     * @return $this
     */
    public function compileBlocks();

    /**
     * @param array $blocks
     *
     * @return $this
     */
    public function importBlocks(array $blocks);

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
     * @param string $pugInput pug input
     * @param string $fileName optional path of the compiled source
     *
     * @throws CompilerException
     *
     * @return null|ElementInterface
     */
    public function compileDocument($pugInput, $fileName = null);

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

    /**
     * @return NodeInterface
     */
    public function getImportNode();

    /**
     * @param NodeInterface $defaultYieldChildren
     *
     * @return $this
     */
    public function setImportNode(NodeInterface $defaultYieldChildren);

    /**
     * @return bool
     */
    public function isImportNodeYielded();

    /**
     * @param string $mixinName
     * @param NodeInterface $node
     *
     * @return MixinNode
     */
    public function requireMixin($mixinName, $node);

    /**
     * @param string $pugInput pug input
     * @param string $fileName optional path of the compiled source
     *
     * @return string
     */
    public function dump($pugInput, $fileName = null);

    /**
     * @param string $fileName pug input file
     *
     * @return string
     */
    public function dumpFile($fileName);

    /**
     * @return $this
     */
    public function enableDynamicMixins();

    /**
     * @return bool
     */
    public function isDynamicMixinsEnabled();

    /**
     * @param string        $message  A meaningful error message
     * @param NodeInterface $node     Node generating the error
     * @param int           $code     Error code
     * @param \Throwable    $previous Source error
     *
     * @throws CompilerException
     */
    public function throwException($message, $node = null, $code = 0, $previous = null);
}
