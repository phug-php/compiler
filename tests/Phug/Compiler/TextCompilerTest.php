<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\TextCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

/**
 * @coversDefaultClass \Phug\Compiler\TextCompiler
 */
class TextCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers ::<public>
     */
    public function testText()
    {
        $this->assertCompile('Hello', '| Hello');
        $this->assertCompile(
            [
                '<pre>',
                'article'."\n",
                '  p Name'."\n",
                '</pre>',
            ],
            [
                'pre.'."\n",
                '  article'."\n",
                '    p Name'."\n",
            ]
        );
        $this->assertCompile(
            [
                '<p>article'."\n",
                '  <p>Name</p>'."\n",
                '</p>',
            ],
            [
                'p.'."\n",
                '  article'."\n",
                '    #[p Name]'."\n",
            ]
        );
        $this->assertCompile(
            [
                '<ul>'."\n",
                '  <li>foo</li>'."\n",
                '  <li>bar</li>'."\n",
                '  <li>baz</li>'."\n",
                '</ul>',
            ],
            [
                '<ul>'."\n",
                '  <li>foo</li>'."\n",
                '  <li>bar</li>' . "\n",
                '  <li>baz</li>'."\n",
                '</ul>',
            ]
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
            'given to text compiler.'
        );

        $textCompiler = new TextCompiler(new Compiler());
        $textCompiler->compileNode(new ElementNode());
    }
}
