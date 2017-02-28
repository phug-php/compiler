<?php

namespace Phug\Test;

use Phug\Compiler;

abstract class AbstractCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Compiler
     */
    protected $compiler;

    public function setUp()
    {
        $this->compiler = new Compiler([
            'basedir' => __DIR__.'/../templates',
        ]);
    }

    protected function assertCompile($expected, $actual)
    {
        $actual = is_string($actual) ? $actual : implode('', $actual);
        $expected = is_string($expected) ? $expected : implode('', $expected);

        self::assertSame($expected, $this->compiler->compile($actual));
    }

    protected function assertRender($expected, $actual)
    {
        $actual = is_string($actual) ? $actual : implode('', $actual);
        $expected = is_string($expected) ? $expected : implode('', $expected);
        ob_start();
        eval('?>'.$this->compiler->compile($actual));
        $actual = ob_get_contents();
        ob_end_clean();

        self::assertSame($expected, $actual);
    }
}
