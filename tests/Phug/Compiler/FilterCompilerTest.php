<?php

namespace Phug\Test\Compiler;

use Phug\Ast\NodeInterface;
use Phug\Compiler;
use Phug\Compiler\FilterCompiler;
use Phug\Formatter\Element\DocumentElement;
use Phug\Formatter\Element\TextElement;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\Node\FilterNode;
use Phug\Parser\Node\TextNode;
use Phug\Test\AbstractCompilerTest;
use Pug\Filter\CoffeeScript;

/**
 * @coversDefaultClass \Phug\Compiler\FilterCompiler
 */
class FilterCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::compileText
     * @covers ::<public>
     * @covers \Phug\AbstractNodeCompiler::getTextChildren
     */
    public function testCompile()
    {
        $compiler = new Compiler([
            'filters' => [
                'js' => function ($contents) {
                    return "<script>\n$contents\n</script>";
                },
            ],
        ]);
        self::assertSame(
            "<body><script>\n".
            "(function () {\n".
            "  console.log(\"Foo\");\n".
            "})()\n".
            '</script></body>',
            $compiler->compile(
                'body'."\n".
                '  :js'."\n".
                '    (function () {'."\n".
                '      console.log("Foo");'."\n".
                '    })()'
            )
        );
        $filter = new FilterNode();
        $filter->setName('js');
        $text1 = new TextNode();
        $text1->setValue('(function () {');
        $text2 = new TextNode();
        $text2->setValue('console.log("Foo");');
        $text3 = new TextNode();
        $text3->setValue('})()');
        $text1->appendChild($text2);
        $filter->appendChild($text1);
        $filter->appendChild($text3);
        self::assertSame(
            "<script>\n".
            "(function () {\n".
            "  console.log(\"Foo\");\n".
            "})()\n".
            '</script>',
            $compiler->compileNode($filter)->getValue()
        );
        //parse
    }

    /**
     * @group i
     * @covers ::compileText
     * @covers ::<public>
     * @covers \Phug\AbstractNodeCompiler::getTextChildren
     */
    public function testLegacyFilters()
    {
        $compiler = new Compiler([
            'filters' => [
                'coffee' => CoffeeScript::class,
            ],
        ]);
        self::assertSame(
            '<script>'.
            "(function() {\n".
            "  return console.log(\"Foo\");\n".
            "})();\n".
            '</script>',
            $compiler->compile(
                'script'."\n".
                '  :coffee'."\n".
                '    do ->'."\n".
                '      console.log "Foo"'
            )
        );
    }

    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testWrongFilterException()
    {
        $this->expectMessageToBeThrown(
            'Unknown filter j-s.'
        );

        $compiler = new Compiler([
            'filters' => [
                'js' => function ($contents) {
                    return $contents;
                },
            ],
        ]);
        $compiler->compile(
            'body'."\n".
            '  :j-s'."\n".
            '    (function () {'."\n".
            '      console.log("Foo");'."\n".
            '    })()'
        );
    }

    /**
     * @covers ::<public>
     */
    public function testFilterOption()
    {
        $compiler = new Compiler([
            'filters' => [
                'foo' => function ($contents, $options) {
                    return $contents.$options['opt'];
                },
            ],
        ]);
        self::assertSame(
            '21',
            $compiler->compile(
                ':foo(opt=1) 2'
            )
        );
    }

    /**
     * @covers            ::compileText
     * @expectedException \Phug\CompilerException
     */
    public function testFilterChildrenException()
    {
        $this->expectMessageToBeThrown(
            'Unexpected Phug\\Formatter\\Element\\DocumentElement in foo filter.'
        );

        $compiler = new Compiler([
            'post_compile_node' => [
                function (NodeInterface &$element) {
                    if ($element instanceof TextElement) {
                        $element = new DocumentElement();
                    }
                },
            ],
            'filters' => [
                'foo' => function ($contents) {
                    return $contents;
                },
            ],
        ]);
        $compiler->compile(
            'body'."\n".
            '  :foo text'
        );
    }

    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        $this->expectMessageToBeThrown(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to filter compiler.'
        );

        $filterCompiler = new FilterCompiler(new Compiler());
        $filterCompiler->compileNode(new ElementNode());
    }
}
