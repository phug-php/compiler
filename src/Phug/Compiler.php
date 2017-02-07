<?php

namespace Phug;

use Phug\Compiler\AssignementCompiler;
use Phug\Compiler\AssignementListCompiler;
use Phug\Compiler\AttributeCompiler;
use Phug\Compiler\AttributeListCompiler;
use Phug\Compiler\BlockCompiler;
use Phug\Compiler\CaseCompiler;
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
use Phug\Compiler\MixinCallCompiler;
use Phug\Compiler\MixinCompiler;
use Phug\Compiler\TextCompiler;
use Phug\Compiler\VariableCompiler;
use Phug\Compiler\WhenCompiler;
use Phug\Compiler\WhileCompiler;
use Phug\Formatter\Format\BasicFormat;
use Phug\Formatter\Format\FramesetFormat;
use Phug\Formatter\Format\HtmlFormat;
use Phug\Formatter\Format\MobileFormat;
use Phug\Formatter\Format\OneDotOneFormat;
use Phug\Formatter\Format\PlistFormat;
use Phug\Formatter\Format\StrictFormat;
use Phug\Formatter\Format\TransitionalFormat;
use Phug\Formatter\Format\XmlFormat;
use Phug\Formatter\FormatInterface;
use Phug\Parser\Node\AssignementListNode;
use Phug\Parser\Node\AssignementNode;
use Phug\Parser\Node\AttributeListNode;
use Phug\Parser\Node\AttributeNode;
use Phug\Parser\Node\BlockNode;
use Phug\Parser\Node\CaseNode;
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
use Phug\Util\OptionInterface;
use Phug\Util\Partial\OptionTrait;

class Compiler implements OptionInterface, CompilerInterface
{
    use OptionTrait;

    private $format;

    private $formatter;

    private $parser;

    private $nodeCompilers;

    private $namedCompilers;

    public function __construct(array $options = null)
    {
        $this->options = array_replace_recursive([
            'parser_class_name'    => Parser::class,
            'parser_options'       => [],
            'formatter_class_name' => Formatter::class,
            'formatter_options'    => [],
            'default_format'       => BasicFormat::class,
            'formats'              => [
                'basic'        => BasicFormat::class,
                'frameset'     => FramesetFormat::class,
                'html'         => HtmlFormat::class,
                'mobile'       => MobileFormat::class,
                '1.1'          => OneDotOneFormat::class,
                'plist'        => PlistFormat::class,
                'strict'       => StrictFormat::class,
                'transitional' => TransitionalFormat::class,
                'xml'          => XmlFormat::class,
            ],
            'node_compilers'       => [
                AssignementListNode::class => AssignementListCompiler::class,
                AssignementNode::class     => AssignementCompiler::class,
                AttributeListNode::class   => AttributeListCompiler::class,
                AttributeNode::class       => AttributeCompiler::class,
                BlockNode::class           => BlockCompiler::class,
                CaseNode::class            => CaseCompiler::class,
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
        ], $options ?: []);

        $parserClassName = $this->options['parser_class_name'];

        if ($parserClassName !== Parser::class && !is_a($parserClassName, Parser::class, true)) {
            throw new CompilerException(
                "Passed parser class $parserClassName is ".
                'not a valid '.Parser::class
            );
        }

        $this->parser = new $parserClassName($this->options['parser_options']);

        $formatterClassName = $this->options['formatter_class_name'];

        if ($formatterClassName !== Formatter::class && !is_a($formatterClassName, Formatter::class, true)) {
            throw new CompilerException(
                "Passed formatter class $formatClassName is ".
                'not a valid '.Formatter::class
            );
        }

        $this->formatter = new $formatterClassName($this->options['formatter_options']);

        $formatClassName = $this->options['default_format'];

        if (!is_a($formatClassName, FormatInterface::class, true)) {
            throw new CompilerException(
                "Passed default format class $formatClassName must ".
                'implement '.FormatInterface::class
            );
        }

        $this->format = $formatClassName;

        $this->nodeCompilers = [];
        $this->namedCompilers = [];

        foreach ($this->options['node_compilers'] as $className => $handler) {
            $this->setNodeCompiler($className, $handler);
        }
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
     * Set the node compiler for a givent node class name.
     *
     * @param string                       node class name
     * @param NodeCompilerInterface|string handler
     *
     * @return $this
     */
    public function setFormat($doctype, $format)
    {
        if (!is_a($format, FormatInterface::class, true)) {
            throw new \InvalidArgumentException(
                "Passed default format class $format must ".
                'implement '.FormatInterface::class
            );
        }
        $this->setOption(['formats', $doctype], $format);

        return $this;
    }

    /**
     * Set the node compiler for a givent node class name.
     *
     * @param string                       node class name
     * @param NodeCompilerInterface|string handler
     *
     * @return $this
     */
    public function setNodeCompiler($className, $handler)
    {
        if (!is_subclass_of($handler, NodeCompilerInterface::class)) {
            throw new \InvalidArgumentException(
                'Passed node compiler needs to implement '.NodeCompilerInterface::class
            );
        }
        $this->nodeHandlers[$className] = $handler;

        return $this;
    }

    /**
     * Instanciate a new compiler by name or return the previous
     * instancied one with the same name.
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
     * Returns PHTML from pug input.
     *
     * @param string pug input
     *
     * @return string
     */
    public function compileNode(NodeInterface $node)
    {
        foreach ($this->nodeHandlers as $className => $compiler) {
            if (is_a($node, $className)) {
                if (!($compiler instanceof NodeCompilerInterface)) {
                    $compiler = $this->getNamedCompiler($compiler);
                }

                return $compiler->compileNode($node);
            }
        }

        throw new CompilerException(
            'No compiler found able to compile '.get_class($node)
        );
    }

    /**
     * Returns PHTML from pug input.
     *
     * @param string pug input
     *
     * @return string
     */
    public function compile($pugInput)
    {
        $node = $this->parser->parse($pugInput);
        $element = $this->compileNode($node);

        return $this->formatter->format($element, $this->format);
    }
}
