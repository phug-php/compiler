<?php

namespace Phug\Test;

use Phug\Compiler;
use Phug\CompilerModule;

/**
 * @coversDefaultClass Phug\CompilerModule
 */
class CompilerModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::<public>
     */
    public function testModule()
    {
        $copy = null;
        $module = new CompilerModule();
        $module->onPlug(function ($_compiler) use (&$copy) {
            $copy = $_compiler;
        });
        $compiler = new Compiler([
            'modules' => [$module],
        ]);
        self::assertSame($compiler, $copy);
    }
}
