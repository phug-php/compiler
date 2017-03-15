<?php

namespace Phug\Test;

use Phug\Compiler;
use Phug\Formatter;
use Phug\Formatter\Element\CodeElement;
use Phug\Formatter\Element\MarkupElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\NodeInterface;

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
     * @covers \Phug\AbstractNodeCompiler::<public>
     * @covers \Phug\Compiler\DoctypeCompiler::<public>
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
            '<html><input /></html>',
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
     * @covers \Phug\AbstractNodeCompiler::compileParserNode
     * @covers \Phug\AbstractNodeCompiler::<public>
     */
    public function testGetCompiledChildren()
    {
        $forCompiler = new Compiler\ForCompiler($this->compiler);
        $elementNode = new ElementNode();
        $elementNode->setName('section');
        $for = new CodeElement('foreach ($groups as $group)', [
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
     * @expectedException \Phug\CompilerException
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
     * @expectedException \Phug\CompilerException
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
            'Phug\NodeCompilerInterface'
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
     * @covers ::walkOption
     * @covers ::compileNode
     */
    public function testHooks()
    {
        $compiler = new Compiler([
            'pre_compile'  => [
                function (NodeInterface $node) {
                    if ($node instanceof ElementNode) {
                        $node->setName($node->getName().'b');
                    }
                },
            ],
            'post_compile' => [
                function (ElementInterface $element) {
                    if ($element instanceof MarkupElement) {
                        $element->setName($element->getName().'c');
                    }
                },
            ],
        ]);

        self::assertSame('<abc></abc>', $compiler->compile('a'));
    }
}
