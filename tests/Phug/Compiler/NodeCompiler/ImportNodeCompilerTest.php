<?php

namespace Phug\Test\Compiler\NodeCompiler;

use Phug\Compiler;
use Phug\Compiler\NodeCompiler\ImportNodeCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;
use Phug\Test\TestCompiler;

/**
 * @coversDefaultClass \Phug\Compiler\NodeCompiler\ImportNodeCompiler
 */
class ImportNodeCompilerTest extends AbstractCompilerTest
{
    /**
     * @covers            ::<public>
     * @expectedException \Phug\CompilerException
     */
    public function testException()
    {
        $this->expectMessageToBeThrown(
            'Unexpected Phug\Parser\Node\ElementNode '.
            'given to import compiler.'
        );

        $importCompiler = new ImportNodeCompiler(new Compiler());
        $importCompiler->compileNode(new ElementNode());
    }

    /**
     * @covers ::<public>
     * @covers ::getBaseDirectoryForPath
     * @covers ::resolvePath
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::compileNamedBlock
     * @covers \Phug\Compiler\Element\BlockElement::<public>
     * @covers \Phug\Compiler\NodeCompiler\FilterNodeCompiler::compileNode
     */
    public function testInclude()
    {
        $this->assertCompile(
            '<section><div>sample</div></section>',
            'section: include /inc.pug'
        );
        $this->compiler->setOption(['filters', 'upper'], function ($contents) {
            return strtoupper($contents);
        });
        $this->assertCompile(
            '<section>UPPER</section>',
            'section: include:upper /lower.txt'
        );
    }

    /**
     * @covers ::<public>
     * @covers ::getBaseDirectoryForPath
     * @covers ::resolvePath
     * @covers \Phug\Compiler::__clone
     * @covers \Phug\Compiler::setLayout
     * @covers \Phug\Compiler::getBlocksByName
     * @covers \Phug\Compiler::setImportNode
     * @covers \Phug\Compiler::isImportNodeYielded
     * @covers \Phug\Compiler::importBlocks
     * @covers \Phug\Compiler::compileBlocks
     * @covers \Phug\Compiler::compile
     * @covers \Phug\Compiler::compileFile
     * @covers \Phug\Compiler::compileFileIntoElement
     * @covers \Phug\Compiler::getFileName
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::compileNamedBlock
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::hasBlockParent
     * @covers \Phug\Compiler\Element\BlockElement::<public>
     * @covers \Phug\Compiler\Layout::<public>
     */
    public function testExtends()
    {
        $this->assertCompileFile(
            "<section>1\nA2\nA</section>",
            __DIR__ . '/../../../templates/page.pug'
        );
    }

    /**
     * @covers ::<public>
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::hasBlockParent
     */
    public function testDoubleInheritance()
    {
        $this->assertCompile(
            [
                'The message is ""',
            ],
            [
                '| The message is "'."\n",
                'yield'."\n",
                '| "'."\n",
            ]
        );
        $this->assertCompileFile(
            [
                '<div class="window">',
                '<a href="#" class="close">Close</a>',
                '<div class="dialog">',
                '<h1>Alert!</h1>',
                '<p>I\'m an alert!</p>',
                '</div>',
                '</div>',
            ],
            __DIR__ . '/../../../templates/inheritance.alert-dialog.pug'
        );
    }

    /**
     * @covers ::<public>
     * @covers ::getBaseDirectoryForPath
     * @covers ::resolvePath
     */
    public function testExtendsInInclude()
    {
        $this->assertCompileFile(
            "<section>1\nA2\nA</section><section>1\nA2\nA</section>",
            __DIR__ . '/../../../templates/inc-page.pug'
        );
    }

    /**
     * @covers ::<public>
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::compileAnonymousBlock
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::compileNamedBlock
     * @covers \Phug\Compiler\Element\BlockElement::<public>
     * @covers \Phug\Compiler::getImportNode
     */
    public function testYieldInInclude()
    {
        $this->assertCompileFile(
            '<div>foo<p>Hello</p>bar</div>',
            __DIR__ . '/../../../templates/inc-yield.pug'
        );
    }

    /**
     * @covers ::<public>
     * @covers ::isRawTextFile
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::compileNamedBlock
     * @covers \Phug\Compiler\Element\BlockElement::<public>
     */
    public function testIncludeNoExtension()
    {
        $this->assertCompileFile(
            '<p>Pug</p>',
            __DIR__ . '/../../../templates/inc-no-extension.pug'
        );
    }

    /**
     * @covers ::<public>
     * @covers ::isRawTextFile
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::compileNamedBlock
     * @covers \Phug\Compiler\Element\BlockElement::<public>
     */
    public function testIncludeChildren()
    {
        $this->assertCompileFile(
            '<section><div>sample<p>A</p><p>B</p></div></section>',
            __DIR__ . '/../../../templates/inc-children.pug'
        );
    }

    /**
     * @covers ::<public>
     * @covers ::isRawTextFile
     * @covers \Phug\Compiler\NodeCompiler\BlockNodeCompiler::compileNamedBlock
     * @covers \Phug\Compiler\Element\BlockElement::<public>
     */
    public function testIncludeRawText()
    {
        $this->assertCompileFile(
            '<pre><code>var x = "\n here is some \n new lined text";'."\n</code></pre>",
            __DIR__ . '/../../../templates/includes-with-ext-js.pug'
        );
    }

    /**
     * @covers            \Phug\Compiler::compileIntoElement
     * @expectedException \Phug\CompilerException
     */
    public function testCompileIntoElementException()
    {
        $this->expectMessageToBeThrown(
            'Phug\Parser\Node\DocumentNode '.
            'compiled into a value that does not '.
            'implement ElementInterface: string'
        );

        require_once __DIR__ . '/../../TestCompiler.php';
        $compiler = new TestCompiler();
        $compiler->compile('extends layout');
    }

    /**
     * @covers            ::getBaseDirectoryForPath
     * @expectedException \Phug\CompilerException
     */
    public function testBasedirException()
    {
        $this->expectMessageToBeThrown(
            'The "basedir" option is required to use '.
            'includes and extends with "absolute" paths.'
        );

        $compiler = new Compiler();
        $compiler->compile('extends /layout');
    }

    /**
     * @covers            ::getBaseDirectoryForPath
     * @expectedException \Phug\CompilerException
     */
    public function testRelativePathException()
    {
        $this->expectMessageToBeThrown(
            'No source file path provided to get relative paths from it.'
        );

        $compiler = new Compiler();
        $compiler->compile('extends ./relative');
    }

    /**
     * @covers            ::resolvePath
     * @expectedException \Phug\CompilerException
     */
    public function testFileNotFoundException()
    {
        $this->expectMessageToBeThrown(
            "File not found at path '/missing'"
        );

        $compiler = new Compiler([
            'basedir' => __DIR__ . '/../../../templates',
        ]);
        $compiler->compile('include /missing');
    }
}