<?php

namespace Phug\Test;

use Exception;
use Phug\Compiler;

abstract class AbstractCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Compiler
     */
    protected $compiler;

    protected function expectMessageToBeThrown($message)
    {
        if (method_exists($this, 'expectExceptionMessage')) {
            $this->expectExceptionMessage($message);

            return;
        }

        $this->setExpectedException(Exception::class, $message, null);
    }

    public function setUp()
    {
        $this->compiler = new Compiler([
            'basedir' => __DIR__.'/../templates',
            'formatter_options' => [
                'patterns' => [
                    'expression_in_text' => '%s',
                ],
            ],
        ]);
    }

    protected function implodeLines($str)
    {
        return is_string($str) ? $str : implode('', $str);
    }

    protected function assertSameLines($expected, $actual)
    {
        self::assertSame($this->implodeLines($expected), $this->implodeLines($actual));
    }

    protected function assertCompile($expected, $actual)
    {
        return $this->assertSameLines($expected, $this->compiler->compile($this->implodeLines($actual)));
    }

    protected function assertCompileFile($expected, $actual)
    {
        return $this->assertSameLines($expected, $this->compiler->compileFile($actual));
    }

    protected function render($actual, array $options = [], array $variables = [])
    {
        $compiler = $this->compiler;
        $compiler->getFormatter()->setOptionsRecursive($options);
        extract($variables);
        ob_start();
        eval('?>'.$compiler->compile($this->implodeLines($actual)));
        $actual = ob_get_contents();
        ob_end_clean();

        return $actual;
    }

    protected function assertRender($expected, $actual, array $options = [], array $variables = [])
    {
        $actual = $this->render($actual, $options, $variables);

        return $this->assertSameLines($expected, $actual);
    }

    protected function assertRenderFile($expected, $actual, array $options = [])
    {
        $compiler = $this->compiler;
        $compiler->getFormatter()->setOptionsRecursive($options);
        ob_start();
        eval('?>'.$compiler->compileFile($actual));
        $actual = ob_get_contents();
        ob_end_clean();

        return $this->assertSameLines($expected, $actual);
    }
}
