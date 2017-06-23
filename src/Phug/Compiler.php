<?php

namespace Phug;

// Node compilers
use Phug\Compiler\AssignmentCompiler;
use Phug\Compiler\AssignmentListCompiler;
use Phug\Compiler\AttributeCompiler;
use Phug\Compiler\AttributeListCompiler;
use Phug\Compiler\Block;
use Phug\Compiler\BlockCompiler;
use Phug\Compiler\CaseCompiler;
use Phug\Compiler\CodeCompiler;
use Phug\Compiler\CommentCompiler;
use Phug\Compiler\ConditionalCompiler;
use Phug\Compiler\DoCompiler;
use Phug\Compiler\DoctypeCompiler;
use Phug\Compiler\DocumentCompiler;
use Phug\Compiler\EachCompiler;
use Phug\Compiler\ElementCompiler;
use Phug\Compiler\ExpressionCompiler;
use Phug\Compiler\FilterCompiler;
use Phug\Compiler\ForCompiler;
use Phug\Compiler\ImportCompiler;
use Phug\Compiler\Layout;
use Phug\Compiler\MixinCallCompiler;
use Phug\Compiler\MixinCompiler;
use Phug\Compiler\TextCompiler;
use Phug\Compiler\VariableCompiler;
use Phug\Compiler\WhenCompiler;
use Phug\Compiler\WhileCompiler;
// Nodes
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\AssignmentListNode;
use Phug\Parser\Node\AssignmentNode;
use Phug\Parser\Node\AttributeListNode;
use Phug\Parser\Node\AttributeNode;
use Phug\Parser\Node\BlockNode;
use Phug\Parser\Node\CaseNode;
use Phug\Parser\Node\CodeNode;
use Phug\Parser\Node\CommentNode;
use Phug\Parser\Node\ConditionalNode;
use Phug\Parser\Node\DoctypeNode;
use Phug\Parser\Node\DocumentNode;
use Phug\Parser\Node\DoNode;
use Phug\Parser\Node\EachNode;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\Node\ExpressionNode;
use Phug\Parser\Node\FilterNode;
use Phug\Parser\Node\ForNode;
use Phug\Parser\Node\ImportNode;
use Phug\Parser\Node\MixinCallNode;
use Phug\Parser\Node\MixinNode;
use Phug\Parser\Node\TextNode;
use Phug\Parser\Node\VariableNode;
use Phug\Parser\Node\WhenNode;
use Phug\Parser\Node\WhileNode;
use Phug\Parser\NodeInterface;
// Utils
use Phug\Util\AssociativeStorage;
use Phug\Util\ModulesContainerInterface;
use Phug\Util\Partial\ModuleTrait;
use Phug\Util\Partial\OptionTrait;

class Compiler implements ModulesContainerInterface, CompilerInterface
{
    use ModuleTrait;
    use OptionTrait;

    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var array
     */
    private $nodeCompilers;

    /**
     * @var array
     */
    private $namedCompilers;

    /**
     * @var array[array[Block]]
     */
    private $namedBlocks;

    /**
     * @var Layout
     */
    private $layout;

    /**
     * @var AssociativeStorage
     */
    private $mixins;

    /**
     * @var NodeInterface
     */
    private $importNode;

    /**
     * @var bool
     */
    private $importNodeYielded;

    public function __construct(array $options = null)
    {
        $this->setOptionsRecursive([
            'basedir'              => null,
            'extensions'           => ['', '.pug', '.jade'],
            'default_tag'          => 'div',
            'default_doctype'      => 'html',
            'pre_compile'          => [],
            'pre_compile_node'     => [],
            'post_compile'         => [],
            'post_compile_node'    => [],
            'filters'              => [],
            'parser_class_name'    => Parser::class,
            'parser_options'       => [],
            'formatter_class_name' => Formatter::class,
            'formatter_options'    => [],
            'mixins_storage_mode'  => AssociativeStorage::REPLACE,
            'modules'              => [],
            'node_compilers'       => [
                AssignmentListNode::class  => AssignmentListCompiler::class,
                AssignmentNode::class      => AssignmentCompiler::class,
                AttributeListNode::class   => AttributeListCompiler::class,
                AttributeNode::class       => AttributeCompiler::class,
                BlockNode::class           => BlockCompiler::class,
                CaseNode::class            => CaseCompiler::class,
                CodeNode::class            => CodeCompiler::class,
                CommentNode::class         => CommentCompiler::class,
                ConditionalNode::class     => ConditionalCompiler::class,
                DoctypeNode::class         => DoctypeCompiler::class,
                DocumentNode::class        => DocumentCompiler::class,
                DoNode::class              => DoCompiler::class,
                EachNode::class            => EachCompiler::class,
                ElementNode::class         => ElementCompiler::class,
                ExpressionNode::class      => ExpressionCompiler::class,
                FilterNode::class          => FilterCompiler::class,
                ForNode::class             => ForCompiler::class,
                ImportNode::class          => ImportCompiler::class,
                MixinCallNode::class       => MixinCallCompiler::class,
                MixinNode::class           => MixinCompiler::class,
                TextNode::class            => TextCompiler::class,
                VariableNode::class        => VariableCompiler::class,
                WhenNode::class            => WhenCompiler::class,
                WhileNode::class           => WhileCompiler::class,
            ],
        ]);
        $this->setOptionsRecursive($options ?: []);

        $this->setExpectedModuleType(CompilerModuleInterface::class);
        $this->addModules($this->getOption('modules'));

        $parserClassName = $this->getOption('parser_class_name');

        if ($parserClassName !== Parser::class && !is_a($parserClassName, Parser::class, true)) {
            throw new CompilerException(
                "Passed parser class $parserClassName is ".
                'not a valid '.Parser::class
            );
        }

        $this->parser = new $parserClassName($this->getOption('parser_options'));

        $formatterClassName = $this->getOption('formatter_class_name');

        if ($formatterClassName !== Formatter::class && !is_a($formatterClassName, Formatter::class, true)) {
            throw new CompilerException(
                "Passed formatter class $formatterClassName is ".
                'not a valid '.Formatter::class
            );
        }

        $this->formatter = new $formatterClassName($this->getOption('formatter_options'));

        $this->nodeCompilers = [];
        $this->namedCompilers = [];

        foreach ($this->getOption('node_compilers') as $className => $handler) {
            $this->setNodeCompiler($className, $handler);
        }

        $this->mixins = new AssociativeStorage(
            'mixin',
            $this->getOption('mixins_storage_mode')
        );
    }

    /**
     * @param NodeInterface $importNode
     *
     * @return $this
     */
    public function setImportNode(NodeInterface $importNode)
    {
        $this->importNode = $importNode;
        $this->importNodeYielded = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function isImportNodeYielded()
    {
        return (bool) $this->importNodeYielded;
    }

    /**
     * @return NodeInterface
     */
    public function getImportNode()
    {
        $this->importNodeYielded = true;

        return $this->importNode;
    }

    /**
     * Reset layout and compilers cache on clone.
     */
    public function __clone()
    {
        $this->layout = null;
        $this->namedCompilers = [];
    }

    /**
     * Return the current layout extended if set.
     *
     * @return Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Set the current layout to extend.
     *
     * @param Layout $layout layout extended
     *
     * @return $this
     */
    public function setLayout(Layout $layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Returns the currently used Parser instance.
     *
     * @return Parser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Returns the currently used Formatter instance.
     *
     * @return Formatter
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * @return AssociativeStorage
     */
    public function getMixins()
    {
        return $this->mixins;
    }

    /**
     * Set the node compiler for a given node class name.
     *
     * @param string                       $className node class name
     * @param NodeCompilerInterface|string $handler   handler
     *
     * @return $this
     */
    public function setNodeCompiler($className, $handler)
    {
        if (!is_subclass_of($handler, NodeCompilerInterface::class)) {
            throw new \InvalidArgumentException(
                'Passed node compiler needs to implement '.
                NodeCompilerInterface::class.'. '.
                (is_object($handler) ? get_class($handler) : $handler).
                ' given.'
            );
        }

        $this->nodeCompilers[$className] = $handler;

        return $this;
    }

    /**
     * Create a new compiler instance by name or return the previous
     * instance with the same name.
     *
     * @param string $compiler name
     *
     * @return NodeCompilerInterface
     */
    private function getNamedCompiler($compiler)
    {
        if (!isset($this->namedCompilers[$compiler])) {
            $this->namedCompilers[$compiler] = new $compiler($this);
        }

        return $this->namedCompilers[$compiler];
    }

    /**
     * Return list of blocks for a given name.
     *
     * @param $name
     *
     * @return mixed
     */
    public function &getBlocksByName($name)
    {
        if (!isset($this->namedBlocks[$name])) {
            $this->namedBlocks[$name] = [];
        }

        return $this->namedBlocks[$name];
    }

    /**
     * Returns lists of blocks grouped by name.
     *
     * @return array
     */
    public function getBlocks()
    {
        return $this->namedBlocks;
    }

    /**
     * Append a named hook in a option list.
     *
     * @param string   $event   event listened
     * @param string   $name    hook name
     * @param callable $handler action called
     *
     * @return $this
     */
    public function addHook($event, $name, callable $handler)
    {
        if (is_array($this->getOption($event))) {
            $this->setOption([$event, $name], $handler);
        }

        return $this;
    }

    protected function walkOption($option, callable $handler)
    {
        $array = $this->getOption($option);
        array_walk($array, $handler);
    }

    /**
     * Returns PHTML from pug node input.
     *
     * @param NodeInterface    $node   input
     * @param ElementInterface $parent optional parent element
     *
     * @throws CompilerException
     *
     * @return ElementInterface
     */
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        $this->walkOption('pre_compile_node', function (callable $preCompile) use (&$node) {
            $preCompile($node, $this);
        });
        foreach ($this->nodeCompilers as $className => $compiler) {
            if (is_a($node, $className)) {
                if (!($compiler instanceof NodeCompilerInterface)) {
                    $compiler = $this->getNamedCompiler($compiler);
                }

                $element = $compiler->compileNode($node, $parent);

                if ($element instanceof ElementInterface && !($element instanceof Block)) {
                    $this->walkOption('post_compile_node', function (callable $postCompile) use (&$element) {
                        $postCompile($element, $this);
                    });
                }

                return $element;
            }
        }

        throw new CompilerException(
            'No compiler found able to compile '.get_class($node)
        );
    }

    /**
     * Replace a block by its nodes.
     *
     * @param Block $block
     * @param array $children
     */
    public function replaceBlock(Block $block, array $children = null)
    {
        if ($parent = $block->getParent()) {
            foreach (array_reverse($children ?: $block->getChildren()) as $child) {
                $parent->insertAfter($block, $child);
            }
            $block->remove();
        }
    }

    /**
     * Replace each block by its compiled children.
     *
     * @param NodeInterface $fallbackNode
     *
     * @throws CompilerException
     *
     * @return $this
     */
    public function compileBlocks()
    {
        foreach ($this->getBlocks() as $name => $blocks) {
            foreach ($blocks as $block) {
                if (!($block instanceof Block)) {
                    throw new CompilerException(
                        'Unexpected block for the name '.$name
                    );
                }
                $this->replaceBlock($block);
            }
        }

        return $this;
    }

    /**
     * Returns PHTML from pug input.
     *
     * @param string $pugInput pug input
     * @param string $fileName optional path of the compiled source
     *
     * @return string
     */
    public function compile($pugInput, $fileName = null)
    {
        $this->walkOption('pre_compile', function (callable $preCompile) use (&$pugInput) {
            $pugInput = $preCompile($pugInput, $this);
        });
        $element = $this->compileIntoElement($pugInput, $fileName);
        $layout = $this->getLayout();
        $blocksCompiler = $this;
        if ($layout) {
            $element = $layout->getDocument();
            $blocksCompiler = $layout->getCompiler();
        }
        $blocksCompiler->compileBlocks();
        $this->formatter->initDependencies();
        $phtml = $this->formatter->format($element);
        $phtml = $this->formatter->formatDependencies().$phtml;
        $this->walkOption('post_compile', function (callable $preCompile) use (&$phtml) {
            $phtml = $preCompile($phtml, $this);
        });

        return $phtml;
    }

    /**
     * Returns PHTML from pug input file.
     *
     * @param string $fileName path of the compiled source
     *
     * @return string
     */
    public function compileFile($fileName)
    {
        return $this->compile(file_get_contents($fileName), $fileName);
    }

    /**
     * Returns ElementInterface from pug input.
     *
     * @param string $pugInput pug input
     * @param string $fileName optional path of the compiled source
     *
     * @throws CompilerException
     *
     * @return null|ElementInterface
     */
    public function compileIntoElement($pugInput, $fileName = null)
    {
        $this->fileName = $fileName;
        $this->namedBlocks = [];
        $node = $this->parser->parse($pugInput);
        $element = $this->compileNode($node);

        if ($element && !($element instanceof ElementInterface)) {
            throw new CompilerException(
                get_class($node).
                ' compiled into a value that does not implement ElementInterface: '.
                (is_object($element) ? get_class($element) : gettype($element))
            );
        }

        return $element;
    }

    /**
     * Returns ElementInterface from pug input file.
     *
     * @param string $fileName path of the compiled source
     *
     * @return ElementInterface
     */
    public function compileFileIntoElement($fileName)
    {
        return $this->compileIntoElement(file_get_contents($fileName), $fileName);
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }
}
