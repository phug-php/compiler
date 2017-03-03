<?php

namespace Phug\Test\Compiler;

use Phug\Compiler;
use Phug\Compiler\ImportCompiler;
use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractCompilerTest;

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
     * @group i
     * @covers ::<public>
     * @covers \Phug\Compiler\Block::<public>
     * @covers \Phug\Compiler\Layout::<public>
     */
    public function testExtends()
    {
        $this->assertCompileFile(
            '<section>1A2A</section>',
            __DIR__.'/../../templates/page.pug'
        );
    }
}
