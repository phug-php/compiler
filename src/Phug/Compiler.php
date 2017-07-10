<?php

namespace Phug;

// Node compilers
use Phug\Compiler\Element\BlockElement;
use Phug\Compiler\Layout;
use Phug\Compiler\NodeCompiler\AssignmentListNodeCompiler;
use Phug\Compiler\NodeCompiler\AssignmentNodeCompiler;
use Phug\Compiler\NodeCompiler\AttributeListNodeCompiler;
use Phug\Compiler\NodeCompiler\AttributeNodeCompiler;
use Phug\Compiler\NodeCompiler\BlockNodeCompiler;
use Phug\Compiler\NodeCompiler\CaseNodeCompiler;
use Phug\Compiler\NodeCompiler\CodeNodeCompiler;
use Phug\Compiler\NodeCompiler\CommentNodeCompiler;
use Phug\Compiler\NodeCompiler\ConditionalNodeCompiler;
use Phug\Compiler\NodeCompiler\DoctypeNodeCompiler;
use Phug\Compiler\NodeCompiler\DocumentNodeCompiler;
use Phug\Compiler\NodeCompiler\DoNodeCompiler;
use Phug\Compiler\NodeCompiler\EachNodeCompiler;
use Phug\Compiler\NodeCompiler\ElementNodeCompiler;
use Phug\Compiler\NodeCompiler\ExpressionNodeCompiler;
use Phug\Compiler\NodeCompiler\FilterNodeCompiler;
use Phug\Compiler\NodeCompiler\ForNodeCompiler;
use Phug\Compiler\NodeCompiler\ImportNodeCompiler;
use Phug\Compiler\NodeCompiler\MixinCallNodeCompiler;
use Phug\Compiler\NodeCompiler\MixinNodeCompiler;
use Phug\Compiler\NodeCompiler\TextNodeCompiler;
use Phug\Compiler\NodeCompiler\VariableNodeCompiler;
use Phug\Compiler\NodeCompiler\WhenNodeCompiler;
use Phug\Compiler\NodeCompiler\WhileNodeCompiler;
// Nodes
use Phug\Compiler\NodeCompilerInterface;
use Phug\Formatter\Element\CodeElement;
use Phug\Formatter\Element\ExpressionElement;
use Phug\Formatter\Element\TextElement;
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
use Phug\Util\ModuleContainerInterface;
use Phug\Util\Partial\ModuleContainerTrait;
use Phug\Util\PugFileLocationInterface;

class Compiler implements ModuleContainerInterface, CompilerInterface
{
    use ModuleContainerTrait;

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
    private $currentNode;

    /**
     * @var NodeInterface
     */
    private $importNode;

    /**
     * @var bool
     */
    private $importNodeYielded;

    /**
     * @var bool
     */
    private $dynamicMixinsEnabled;

    public function __construct(array $options = null)
    {
        $this->setOptionsRecursive([
            'basedir'              => null,
            'debug'                => false,
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
                AssignmentListNode::class  => AssignmentListNodeCompiler::class,
                AssignmentNode::class      => AssignmentNodeCompiler::class,
                AttributeListNode::class   => AttributeListNodeCompiler::class,
                AttributeNode::class       => AttributeNodeCompiler::class,
                BlockNode::class           => BlockNodeCompiler::class,
                CaseNode::class            => CaseNodeCompiler::class,
                CodeNode::class            => CodeNodeCompiler::class,
                CommentNode::class         => CommentNodeCompiler::class,
                ConditionalNode::class     => ConditionalNodeCompiler::class,
                DoctypeNode::class         => DoctypeNodeCompiler::class,
                DocumentNode::class        => DocumentNodeCompiler::class,
                DoNode::class              => DoNodeCompiler::class,
                EachNode::class            => EachNodeCompiler::class,
                ElementNode::class         => ElementNodeCompiler::class,
                ExpressionNode::class      => ExpressionNodeCompiler::class,
                FilterNode::class          => FilterNodeCompiler::class,
                ForNode::class             => ForNodeCompiler::class,
                ImportNode::class          => ImportNodeCompiler::class,
                MixinCallNode::class       => MixinCallNodeCompiler::class,
                MixinNode::class           => MixinNodeCompiler::class,
                TextNode::class            => TextNodeCompiler::class,
                VariableNode::class        => VariableNodeCompiler::class,
                WhenNode::class            => WhenNodeCompiler::class,
                WhileNode::class           => WhileNodeCompiler::class,
            ],
        ]);
        $this->setOptionsRecursive($options ?: []);

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
     * @return NodeInterface
     */
    public function getCurrentNode()
    {
        return $this->currentNode;
    }

    /**
     * @return $this
     */
    public function enableDynamicMixins()
    {
        $this->dynamicMixinsEnabled = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDynamicMixinsEnabled()
    {
        return $this->dynamicMixinsEnabled;
    }

    /**
     * @param string        $mixinName
     * @param NodeInterface $node
     *
     * @return MixinNode
     */
    public function requireMixin($mixinName, $node)
    {
        /** @var MixinNode $declaration */
        $declaration = $this->getMixins()->findFirstByName($mixinName);
        if (!$declaration) {
            $this->throwException(
                'Unknown '.$mixinName.' mixin called.',
                $node
            );
        }
        if (isset($declaration->mixinConstructor)) {
            call_user_func($declaration->mixinConstructor);
            unset($declaration->mixinConstructor);
        }

        return $declaration;
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

    private function convertBlocksToDynamicCalls($element)
    {
        if ($element instanceof BlockElement) {
            $expression = new ExpressionElement('$__pug_children');
            $expression->preventFromTransformation();

            return $expression;
        }

        if ($element instanceof ElementInterface) {
            $element->setChildren(array_map(
                [$this, 'convertBlocksToDynamicCalls'],
                $element->getChildren()
            ));
        }

        return $element;
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
        $this->currentNode = $node;
        $this->walkOption('pre_compile_node', function (callable $preCompile) use (&$node) {
            $preCompile($node, $this);
        });
        foreach ($this->nodeCompilers as $className => $compiler) {
            if (is_a($node, $className)) {
                if (!($compiler instanceof NodeCompilerInterface)) {
                    $compiler = $this->getNamedCompiler($compiler);
                }

                $element = $compiler->compileNode($node, $parent);

                if ($element instanceof ElementInterface && !($element instanceof BlockElement)) {
                    $this->walkOption('post_compile_node', function (callable $postCompile) use (&$element) {
                        $postCompile($element, $this);
                    });
                }

                return $element;
            }
        }

        $this->throwException(
            'No compiler found able to compile '.get_class($node),
            $node
        );
    }

    /**
     * Replace a block by its nodes.
     *
     * @param BlockElement $block
     * @param array        $children
     */
    public function replaceBlock(BlockElement $block, array $children = null)
    {
        if ($parent = $block->getParent()) {
            foreach (array_reverse($children ?: $block->getChildren()) as $child) {
                $parent->insertAfter($block, $child);
            }
            $previous = $block->getPreviousSibling();
            if ($previous instanceof TextElement) {
                $previous->setEnd(true);
            }
            $block->remove();
        }
    }

    /**
     * Import blocks named lists into the compiler.
     *
     * @param array $blocks
     *
     * @return $this|void
     */
    public function importBlocks(array $blocks)
    {
        foreach ($blocks as $name => $list) {
            foreach ($list as $block) {
                /* @var BlockElement $block */
                $block->addCompiler($this);
            }
        }
    }

    /**
     * Replace each block by its compiled children.
     *
     * @throws CompilerException
     *
     * @return $this
     */
    public function compileBlocks()
    {
        do {
            $blockProceeded = 0;
            foreach ($this->getBlocks() as $name => $blocks) {
                foreach ($blocks as $block) {
                    if (!($block instanceof BlockElement)) {
                        throw new CompilerException(
                            'Unexpected block for the name '.$name
                        );
                    }
                    /** @var BlockElement $block */
                    if ($block->hasParent()) {
                        $this->replaceBlock($block);
                        $blockProceeded++;
                    }
                }
            }
        } while ($blockProceeded);

        return $this;
    }

    /**
     * Dump a debug tre for a given pug input.
     *
     * @param string $pugInput pug input
     * @param string $fileName optional path of the compiled source
     *
     * @return string
     */
    public function dump($pugInput, $fileName = null)
    {
        $element = $this->compileDocument($pugInput, $fileName);

        return $element instanceof ElementInterface
            ? $element->dump()
            : var_export($element, true);
    }

    /**
     * Dump a debug tre for a given pug input.
     *
     * @param string $fileName pug input file
     *
     * @return string
     */
    public function dumpFile($fileName)
    {
        return $this->dump(file_get_contents($fileName), $fileName);
    }

    /**
     * Returns ElementInterface from pug input with all layouts and
     * blocks compiled.
     *
     * @param string $pugInput pug input
     * @param string $fileName optional path of the compiled source
     *
     * @throws CompilerException
     *
     * @return null|ElementInterface
     */
    public function compileDocument($pugInput, $fileName = null)
    {
        $this->formatter->initDependencies();
        $element = $this->compileIntoElement($pugInput, $fileName);
        $layout = $this->getLayout();
        $blocksCompiler = $this;
        if ($layout) {
            $element = $layout->getDocument();
            $blocksCompiler = $layout->getCompiler();
        }
        $blocksCompiler->compileBlocks();
        if ($this->isDynamicMixinsEnabled()) {
            $code = '';
            foreach ($this->getMixins() as $mixin) {
                $argumentsNames = [];
                foreach ($mixin->getAttributes() as $attribute) {
                    /* @var AttributeNode $attribute */
                    $argumentsNames[] = var_export(
                        trim(str_replace('$', '', $attribute->getName())),
                        true
                    );
                }
                $content = implode("\n", [
                    'foreach (['.implode(', ', $argumentsNames).'] as $__pug_index => $__pug_name) {',
                    '    if (mb_substr($__pug_name, 0, 3) === "...") {',
                    '        ${mb_substr($__pug_name, 3)} = array_slice($__pug_params["arguments"], $__pug_index);',
                    '        break;',
                    '    }',
                    '    $$__pug_name = isset($__pug_params["arguments"][$__pug_index]) '.
                        '? $__pug_params["arguments"][$__pug_index] '.
                        ': null;',
                    '}',
                    '$__pug_children = $__pug_params["children"];',
                ]);
                /* @var MixinNode $mixin */
                foreach ($mixin->getChildren() as $child) {
                    /* @var NodeInterface $child */
                    $childElement = $child instanceof ElementInterface
                        ? $child
                        : $this->compileNode($child);
                    $content .= "\n".'?>'.$this->formatter->format(
                        $this->convertBlocksToDynamicCalls($childElement)
                    ).'<?php';
                }
                $code .= var_export($mixin->getName(), true).
                    ' => function ($__pug_params) {'."\n".
                        'foreach ($__pug_params["globals"] as $key => &$value) {'."\n".
                        '    $$key = &$value;'."\n".
                        '}'."\n".
                        '$attributes = $__pug_params["attributes"];'."\n".
                        $content.
                    "\n},\n";
            }
            $declarations = new CodeElement(
                '$__pug_mixins = ['."\n".$code.'];'
            );

            $declarations->preventFromTransformation();

            $element->prependChild($declarations);
        }

        return $element;
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
        $element = $this->compileDocument($pugInput, $fileName);
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
     * @throws \Exception
     *
     * @return null|ElementInterface
     */
    public function compileIntoElement($pugInput, $fileName = null)
    {
        $this->fileName = $fileName;
        $this->namedBlocks = [];
        try {
            $node = $this->parser->parse($pugInput);
        } catch (PugFileLocationInterface $exception) {
            if ($fileName && !$exception->getPugFile()) {
                $exception->setPugFile($fileName);
            }

            throw $exception;
        }
        $element = $this->compileNode($node);

        if ($element && !($element instanceof ElementInterface)) {
            $this->throwException(
                get_class($node).
                ' compiled into a value that does not implement ElementInterface: '.
                (is_object($element) ? get_class($element) : gettype($element)),
                $node
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

    public function getModuleBaseClassName()
    {
        return CompilerModuleInterface::class;
    }

    /**
     * Throws a compiler-exception.
     *
     * The current file, line and offset of the exception
     * get automatically appended to the exception
     *
     * @param string        $message  A meaningful error message
     * @param NodeInterface $node     Node generating the error
     * @param int           $code     Error code
     * @param \Throwable    $previous Source error
     *
     * @throws CompilerException
     */
    public function throwException($message, $node = null, $code = 0, $previous = null)
    {
        $pattern = "Failed to compile: %s Line: %s \nOffset: %s";

        if ($this->fileName) {
            $pattern .= "\nPath: ".$this->fileName;
        }
        $node = $node instanceof NodeInterface ? $node : null;

        throw new CompilerException(
            vsprintf($pattern, [
                $message,
                $node ? $node->getLine() : '',
                $node ? $node->getOffset() : '',
            ]),
            $code,
            $previous,
            $this->fileName,
            $node ? $node->getLine() : null,
            $node ? $node->getOffset() : null
        );
    }
}
