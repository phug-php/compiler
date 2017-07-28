<?php

namespace Phug;

// Node compilers
use Phug\Compiler\Element\BlockElement;
use Phug\Compiler\Event\CompileEvent;
use Phug\Compiler\Event\ElementEvent;
use Phug\Compiler\Event\NodeEvent;
use Phug\Compiler\Event\OutputEvent;
use Phug\Compiler\Layout;
use Phug\Compiler\Locator\FileLocator;
use Phug\Compiler\LocatorInterface;
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
use Phug\Util\SourceLocation;

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
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var string
     */
    private $path;

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

    public function __construct($options = null)
    {
        $this->setOptionsDefaults($options ?: [], [
            'paths'                => [],
            'debug'                => false,
            'extensions'           => ['', '.pug', '.jade'],
            'default_tag'          => 'div',
            'default_doctype'      => 'html',
            'on_compile'           => null,
            'on_output'            => null,
            'on_node'              => null,
            'on_element'           => null,
            'filters'              => [],
            'parser_class_name'    => Parser::class,
            'formatter_class_name' => Formatter::class,
            'locator_class_name'   => FileLocator::class,
            'mixins_storage_mode'  => AssociativeStorage::REPLACE,
            'compiler_modules'     => [],
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

        //Initialize parser to parse source code into an AST
        $parserClassName = $this->getOption('parser_class_name');

        if (!is_a($parserClassName, Parser::class, true)) {
            throw new \InvalidArgumentException(
                "Passed parser class $parserClassName is ".
                'not a valid '.Parser::class
            );
        }

        $this->parser = new $parserClassName($this->getOptions());

        //Initialize the formatter to turn elements into PHTML
        $formatterClassName = $this->getOption('formatter_class_name');

        if (!is_a($formatterClassName, Formatter::class, true)) {
            throw new \InvalidArgumentException(
                "Passed formatter class $formatterClassName is ".
                'not a valid '.Formatter::class
            );
        }

        $this->formatter = new $formatterClassName($this->getOptions());

        //Initialize the Locator to locate sources
        $locatorClassName = $this->getOption('locator_class_name');

        if (!is_a($locatorClassName, LocatorInterface::class, true)) {
            throw new \InvalidArgumentException(
                "Passed locator class $locatorClassName is ".
                'not a valid '.LocatorInterface::class
            );
        }

        $this->locator = new $locatorClassName($this->getOptions());

        $this->nodeCompilers = [];
        $this->namedCompilers = [];

        foreach ($this->getOption('node_compilers') as $className => $handler) {
            $this->setNodeCompiler($className, $handler);
        }

        $this->mixins = new AssociativeStorage(
            'mixin',
            $this->getOption('mixins_storage_mode')
        );

        if ($onCompile = $this->getOption('on_compile')) {
            $this->attach(CompilerEvent::COMPILE, $onCompile);
        }

        if ($onOutput = $this->getOption('on_output')) {
            $this->attach(CompilerEvent::OUTPUT, $onOutput);
        }

        if ($onNode = $this->getOption('on_node')) {
            $this->attach(CompilerEvent::NODE, $onNode);
        }

        if ($onElement = $this->getOption('on_element')) {
            $this->attach(CompilerEvent::ELEMENT, $onElement);
        }

        $this->addModules($this->getOption('compiler_modules'));
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

    public function pushPath($path)
    {
        $paths = $this->getOption('paths');
        $paths[] = $path;
        $this->setOption('paths', $paths);

        return $this;
    }

    public function popPath()
    {
        $paths = $this->getOption('paths');
        array_pop($paths);
        $this->setOption('paths', $paths);

        return $this;
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

    public function locate($path)
    {
        return $this->locator->locate(
            $path,
            $this->getOption('paths'),
            $this->getOption('extensions')
        );
    }

    public function resolve($path)
    {
        $resolvePath = $this->locate($path);

        if (!$resolvePath) {
            $this->throwException(sprintf(
                "Source file %s not found \nPaths: %s \nExtensions: %s",
                $path,
                implode(', ', $this->getOption('paths')),
                implode(', ', $this->getOption('extensions'))
            ));
        }

        return $resolvePath;
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
        $nodeEvent = new NodeEvent($node);
        $this->trigger($nodeEvent);
        $node = $nodeEvent->getNode();

        $this->currentNode = $node;

        foreach ($this->nodeCompilers as $className => $compiler) {
            if (is_a($node, $className)) {
                if (!($compiler instanceof NodeCompilerInterface)) {
                    $compiler = $this->getNamedCompiler($compiler);
                }

                $element = $compiler->compileNode($node, $parent);

                if ($element instanceof ElementInterface && !($element instanceof BlockElement)) {
                    $elementEvent = new ElementEvent($nodeEvent, $element);
                    $this->trigger($elementEvent);
                    $element = $elementEvent->getElement();
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
                        $this->throwException(
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
     * @param string $input pug input
     * @param string $path  optional path of the compiled source
     *
     * @return string
     */
    public function dump($input, $path = null)
    {
        $element = $this->compileDocument($input, $path);

        return $element instanceof ElementInterface
            ? $element->dump()
            : var_export($element, true);
    }

    /**
     * Dump a debug tre for a given pug input.
     *
     * @param string $path pug input file
     *
     * @return string
     */
    public function dumpFile($path)
    {
        $path = $this->resolve($path);

        return $this->dump(file_get_contents($path), $path);
    }

    /**
     * Returns ElementInterface from pug input with all layouts and
     * blocks compiled.
     *
     * @param string $input pug input
     * @param string $path  optional path of the compiled source
     *
     * @throws CompilerException
     *
     * @return null|ElementInterface
     */
    public function compileDocument($input, $path = null)
    {
        $element = $this->compileIntoElement($input, $path);
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
     * @param string $input pug input
     * @param string $path  optional path of the compiled source
     *
     * @return string
     */
    public function compile($input, $path = null)
    {
        $event = new CompileEvent($input, $path);
        $this->trigger($event);

        $input = $event->getInput();
        $path = $event->getPath();

        $element = $this->compileDocument($input, $path);

        $output = $this->formatter->format($element);
        $output = $this->formatter->formatDependencies().$output;

        $event = new OutputEvent($output);
        $this->trigger($event);

        return $event->getOutput();
    }

    /**
     * Returns PHTML from pug input file.
     *
     * @param string $path path of the compiled source
     *
     * @return string
     */
    public function compileFile($path)
    {
        $path = $this->resolve($path);

        return $this->compile(file_get_contents($path), $path);
    }

    /**
     * Returns ElementInterface from pug input.
     *
     * @param string $input pug input
     * @param string $path  optional path of the compiled source
     *
     * @throws \Exception
     *
     * @return null|ElementInterface
     */
    public function compileIntoElement($input, $path = null)
    {
        $this->path = $path;
        $this->namedBlocks = [];

        $node = $this->parser->parse($input); //Let exceptions fall through

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
     * @param string $path path of the compiled source
     *
     * @return ElementInterface
     */
    public function compileFileIntoElement($path)
    {
        $path = $this->resolve($path);

        return $this->compileIntoElement(file_get_contents($path), $path);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
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
        $pattern = "Failed to compile: %s \nLine: %s \nOffset: %s";

        $location = $node ? $node->getSourceLocation() : null;

        $path = $location ? $location->getPath() : $this->getPath();
        $line = $location ? $location->getLine() : 0;
        $offset = $location ? $location->getOffset() : 0;
        $offsetLength = $location ? $location->getOffsetLength() : 0;

        if ($path) {
            $pattern .= "\nPath: $path";
        }

        throw new CompilerException(
            new SourceLocation($path, $line, $offset, $offsetLength),
            vsprintf($pattern, [
                $message,
                $line,
                $offset,
            ]),
            $code,
            $previous
        );
    }
}
