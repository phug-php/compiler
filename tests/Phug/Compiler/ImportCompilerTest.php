<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\ImportCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;
use Phug\Test\TestCompiler;

/**
 * @coversDefaultClass \Phug\Compiler\ImportCompiler
 */
class ImportCompilerTest extends AbstractCompilerTest
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

        $importCompiler = new ImportCompiler(new Compiler());
        $importCompiler->compileNode(new ElementNode());
    }

    /**
     * @covers ::<public>
     * @covers ::getBaseDirectoryForPath
     * @covers ::resolvePath
     * @covers \Phug\Compiler\BlockCompiler::compileNamedBlock
     * @covers \Phug\Compiler\Block::<public>
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
     * @covers \Phug\Compiler\BlockCompiler::compileNamedBlock
     * @covers \Phug\Compiler::__clone
     * @covers \Phug\Compiler::setLayout
     * @covers \Phug\Compiler::getBlocksByName
     * @covers \Phug\Compiler::compileBlocks
     * @covers \Phug\Compiler::compile
     * @covers \Phug\Compiler::compileFile
     * @covers \Phug\Compiler::compileFileIntoElement
     * @covers \Phug\Compiler::getFileName
     * @covers \Phug\Compiler\Block::<public>
     * @covers \Phug\Compiler\Layout::<public>
     */
    public function testExtends()
    {
        $this->assertCompileFile(
            '<section>1 A 2 A</section>',
            __DIR__.'/../../templates/page.pug'
        );
    }

    /**
     * @covers ::<public>
     * @covers \Phug\Compiler\ImportCompiler::getBaseDirectoryForPath
     * @covers \Phug\Compiler\ImportCompiler::resolvePath
     */
    public function testExtendsInInclude()
    {
        $this->assertCompileFile(
            '<section>1 A 2 A</section><section>1 A 2 A</section>',
            __DIR__.'/../../templates/inc-page.pug'
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

        include_once __DIR__.'/../TestCompiler.php';
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
            'basedir' => __DIR__.'/../../templates',
        ]);
        $compiler->compile('include /missing');
    }
}
