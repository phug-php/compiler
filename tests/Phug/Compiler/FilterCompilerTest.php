<?php

namespace Phug\Test\Compiler;

use Phug\Ast\NodeInterface;
use Phug\Compiler;
use Phug\Compiler\FilterCompiler;
use Phug\Formatter\Element\DocumentElement;
use Phug\Formatter\Element\TextElement;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\FilterCompiler
 */
class FilterCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::compileText
     * @covers ::<public>
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
            "function foo() {\n".
            "  console.log(\"Foo\");\n".
            "}\n".
            '</script></body>',
            $compiler->compile(
                'body'."\n".
                '  :js'."\n".
                '    function foo() {'."\n".
                '      console.log("Foo");'."\n".
                '    }'
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
            '    function foo() {'."\n".
            '      console.log("Foo");'."\n".
            '    }'
        );
    }

    /**
     * @group i
     * @covers            ::compileText
     * @expectedException \Phug\CompilerException
     */
    public function testFilterChildrenException()
    {
        $this->expectMessageToBeThrown(
            'Unexpected Phug\\Formatter\\Element\\DocumentElement in foo filter.'
        );

        $compiler = new Compiler([
            'post_compile' => [
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
