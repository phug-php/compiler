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
        $this->compiler = new Compiler();
    }

    protected function assertCompile($expected, $actual)
    {
        $actual = is_string($actual) ? $actual : implode('', $actual);
        $expected = is_string($expected) ? $expected : implode('', $expected);

        self::assertSame($expected, $this->compiler->compile($actual));
    }
}
