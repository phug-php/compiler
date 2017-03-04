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
     * @covers                   ::<public>
     * @expectedException        \Phug\CompilerException
     * @expectedExceptionMessage Unexpected Phug\Parser\Node\ElementNode
     * @expectedExceptionMessage given to import compiler.
     */
    public function testException()
    {
        $importCompiler = new ImportCompiler(new Compiler());
        $importCompiler->compileNode(new ElementNode());
    }

    /**
     * @covers ::<public>
     * @covers \Phug\Compiler\Block::<public>
     */
    public function testInclude()
    {
        $this->assertCompile(
            '<section><div>sample</div></section>',
            'section: include /inc.pug'
        );
    }

    /**
     * @covers ::<public>
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
     * @covers                   \Phug\Compiler::compileIntoElement
     * @expectedException        \Phug\CompilerException
     * @expectedExceptionMessage Phug\Parser\Node\DocumentNode
     * @expectedExceptionMessage compiled into a value that does not
     * @expectedExceptionMessage implement ElementInterface: string
     */
    public function testCompileIntoElementException()
    {
        include_once __DIR__.'/../TestCompiler.php';
        $compiler = new TestCompiler();
        $compiler->compile('extends layout');
    }
}
