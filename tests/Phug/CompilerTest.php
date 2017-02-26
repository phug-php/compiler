<?php

namespace Phug\Test;

use Phug\Compiler;
use Phug\Formatter;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser;
use Phug\Parser\Node\ElementNode;

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
            "doctype html\nhtml\n  input"
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
     * @covers                   ::__construct
     * @expectedException        \Phug\CompilerException
     * @expectedExceptionMessage Passed parser class
     * @expectedExceptionMessage Phug\Parser\Node\ElementNode
     * @expectedExceptionMessage is not a valid
     * @expectedExceptionMessage Phug\Parser
     */
    public function testParserClassException()
    {
        new Compiler([
            'parser_class_name' => ElementNode::class,
        ]);
    }

    /**
     * @covers                   ::__construct
     * @expectedException        \Phug\CompilerException
     * @expectedExceptionMessage Passed formatter class
     * @expectedExceptionMessage Phug\Parser\Node\ElementNode
     * @expectedExceptionMessage is not a valid
     * @expectedExceptionMessage Phug\Formatter
     */
    public function testFormatterClassException()
    {
        new Compiler([
            'formatter_class_name' => ElementNode::class,
        ]);
    }

    /**
     * @covers                   ::setNodeCompiler
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Passed node compiler needs to implement
     * @expectedExceptionMessage Phug\CompilerInterface
     */
    public function testSetNodeCompilerException()
    {
        $compiler = new Compiler();
        $compiler->setNodeCompiler(ElementNode::class, ElementNode::class);
    }

    /**
     * @covers                   ::compileNode
     * @expectedException        \Phug\CompilerException
     * @expectedExceptionMessage No compiler found able to compile
     * @expectedExceptionMessage Phug\CompilerInterface
     */
    public function testCompileNodeException()
    {
        include_once __DIR__.'/Node/TestNode.php';
        $compiler = new Compiler();
        $compiler->compileNode(new TestNode());
    }
}
