<?php

namespace Phug;

use Phug\Compiler\DocumentCompiler;
use Phug\Formatter\Format\HtmlFormat;
use Phug\Formatter\FormatInterface;
use Phug\Parser\Node\DocumentNode;
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
            'format_class_name'    => HtmlFormat::class,
            'node_compilers'       => [
                DocumentNode::class => DocumentCompiler::class,
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

        $formatClassName = $this->options['format_class_name'];

        if (!is_a($formatClassName, FormatInterface::class, true)) {
            throw new CompilerException(
                "Passed format class $formatClassName must ".
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
