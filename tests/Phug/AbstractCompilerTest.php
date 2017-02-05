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
}
