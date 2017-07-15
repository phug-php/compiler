<?php

namespace Phug\Test;

use Exception;
use JsPhpize\JsPhpize;
use Phug\Compiler;
use Phug\CompilerEvent;
use Phug\CompilerException;
use Phug\Formatter;
use Phug\Formatter\Element\CodeElement;
use Phug\Formatter\Element\MarkupElement;
use Phug\LexerException;
use Phug\Parser;
use Phug\Parser\Node\ElementNode;
use Phug\ParserException;

/**
 * @coversDefaultClass \Phug\Compiler
 */
class CompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::<public>
     */
    public function testGetters()
    {
        $compiler = new Compiler();

        self::assertInstanceOf(Formatter::class, $compiler->getFormatter());
        self::assertInstanceOf(Parser::class, $compiler->getParser());
    }

    /**
     * @covers ::compileNode
     * @covers ::getNamedCompiler
     */
    public function testCompileNode()
    {
        $compiler = new Compiler();

        self::assertInstanceOf(Formatter::class, $compiler->getFormatter());
        self::assertInstanceOf(Parser::class, $compiler->getParser());

        $section = new ElementNode();
        $section->setName('section');

        /**
         * @var MarkupElement $section
         */
        $section = $compiler->compileNode($section);

        self::assertInstanceOf(MarkupElement::class, $section);
        self::assertSame('section', $section->getName());
    }

    /**
     * @covers ::<public>
     * @covers \Phug\Compiler\AbstractNodeCompiler::<public>
     * @covers \Phug\Compiler\NodeCompiler\DoctypeNodeCompiler::<public>
     */
    public function testCompile()
    {
        // Empty string
        $this->assertCompile('', '');

        // Single tag
        $this->assertCompile('<html></html>', 'html');

        // Children
        $this->assertCompile([
            '<html>',
            '<head></head>',
            '<body></body>',
            '</html>',
        ], [
            "html\n",
            "  head\n",
            "  body\n",
        ]);

        // Doctype
        $this->assertCompile(
            '<!DOCTYPE html><html><input></html>',
            "doctype html\n".
            "html\n".
            '  input'
        );
        $this->assertCompile([
            '<!DOCTYPE html>',
            '<html><input></html>',
            '<!DOCTYPE foobar>',
            '<html><input /></html>',
            '<?xml version="1.0" encoding="utf-8" ?>',
            '<html><input></input></html>',
            '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
            '<html><input /></html>',
        ], [
            "doctype html\n",
            "html: input\n",
            "doctype foobar\n",
            "html: input\n",
            "doctype xml\n",
            "html: input\n",
            "doctype 1.1\n",
            "html: input\n",
        ]);
    }

    /**
     * @covers \Phug\Compiler\AbstractNodeCompiler::compileParserNode
     * @covers \Phug\Compiler\AbstractNodeCompiler::<public>
     */
    public function testGetCompiledChildren()
    {
        $forCompiler = new Compiler\NodeCompiler\ForNodeCompiler($this->compiler);
        $elementNode = new ElementNode();
        $elementNode->setName('section');
        $for = new CodeElement('foreach ($groups as $group)', null, null, [
            new MarkupElement('article'),
            $elementNode,
        ]);
        $compiledChildren = $forCompiler->getCompiledChildren($for, null);

        self::assertSame(1, count($compiledChildren));
        self::assertInstanceOf(MarkupElement::class, $compiledChildren[0]);
        /**
         * @var MarkupElement $markup
         */
        $markup = $compiledChildren[0];
        self::assertSame('section', $markup->getName());
    }

    /**
     * @covers            ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testParserClassException()
    {
        $this->expectMessageToBeThrown(
            'Passed parser class '.
            'Phug\Parser\Node\ElementNode '.
            'is not a valid '.
            'Phug\Parser'
        );

        new Compiler([
            'parser_class_name' => ElementNode::class,
        ]);
    }

    /**
     * @covers            ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testFormatterClassException()
    {
        $this->expectMessageToBeThrown(
            'Passed formatter class '.
            'Phug\Parser\Node\ElementNode '.
            'is not a valid '.
            'Phug\Formatter'
        );

        new Compiler([
            'formatter_class_name' => ElementNode::class,
        ]);
    }

    /**
     * @covers            ::setNodeCompiler
     * @expectedException \InvalidArgumentException
     */
    public function testSetNodeCompilerException()
    {
        $this->expectMessageToBeThrown(
            'Passed node compiler needs to implement '.
            'Phug\Compiler\NodeCompilerInterface. Phug\Parser\Node\ElementNode given.'
        );

        $compiler = new Compiler();
        $compiler->setNodeCompiler(ElementNode::class, ElementNode::class);
    }

    /**
     * @covers            ::compileNode
     * @expectedException \Phug\CompilerException
     */
    public function testCompileNodeException()
    {
        $this->expectMessageToBeThrown(
            'No compiler found able to compile '.
            'Phug\Test\TestNode'
        );

        include_once __DIR__.'/Node/TestNode.php';
        $compiler = new Compiler();
        $compiler->compileNode(new TestNode());
    }

    /**
     * @group hooks
     * @covers ::compileNode
     * @covers ::compile
     */
    public function testHooks()
    {
        $compiler = new Compiler([
            'on_node'  => function (Compiler\Event\NodeEvent $e) {
                $node = $e->getNode();
                if ($node instanceof ElementNode) {
                    $node->setName($node->getName().'b');
                }
            },
            'on_element' => function (Compiler\Event\ElementEvent $e) {
                $element = $e->getElement();
                if ($element instanceof MarkupElement) {
                    $element->setName($element->getName().'c');
                }
            },
        ]);

        self::assertSame('<abc></abc>', $compiler->compile('a'));

        $compiler = new Compiler([
            'on_compile'  => function (Compiler\Event\CompileEvent $e) {
                $e->setInput($e->getInput().' Hello');
            },
            'on_output' => function (Compiler\Event\OutputEvent $e) {
                $e->setOutput('<p>'.$e->getOutput().'</p>');
            },
        ]);

        self::assertSame('<p><a>Hello</a></p>', $compiler->compile('a'));

        $compiler = new Compiler([
            'formatter_options' => [
                'patterns' => [
                    'transform_expression' => function ($jsCode) use (&$compiler) {
                        /** @var JsPhpize $jsPhpize */
                        $jsPhpize = $compiler->getOption('jsphpize_engine');

                        try {
                            return rtrim(trim(preg_replace(
                                '/\{\s*\}$/',
                                '',
                                trim($jsPhpize->compile($jsCode))
                            )), ';');
                        } catch (Exception $e) {
                            if ($e instanceof LexerException ||
                                $e instanceof ParserException ||
                                $e instanceof CompilerException
                            ) {
                                return $jsCode;
                            }

                            throw $e;
                        }
                    },
                ],
            ],
        ]);
        $compiler->attach(CompilerEvent::COMPILE, function (Compiler\Event\CompileEvent $e) use ($compiler) {
            $compiler->setOption('jsphpize_engine', new JsPhpize([
                'catchDependencies' => true,
            ]));
        });

        $compiler->attach(CompilerEvent::OUTPUT, function (Compiler\Event\OutputEvent $e) use ($compiler) {

            /** @var JsPhpize $jsPhpize */
            $jsPhpize = $compiler->getOption('jsphpize_engine');
            $dependencies = $jsPhpize->compileDependencies();
            if ($dependencies !== '') {
                $e->setOutput($compiler->getFormatter()->handleCode($dependencies).$e->getOutput());
            }
            $jsPhpize->flushDependencies();
            $compiler->unsetOption('jsphpize_engine');
        });
        $this->compiler = $compiler;

        $this->assertRender('<p>Hello</p>', 'p=foo.bar', [], [
            'foo' => [
                'bar' => 'Hello',
            ],
        ]);
    }
}
