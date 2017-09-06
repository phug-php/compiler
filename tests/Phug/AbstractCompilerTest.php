<?php

namespace Phug\Test;

use Exception;
use JsPhpize\JsPhpize;
use Phug\Compiler;
use Phug\CompilerEvent;

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
            'paths'    => [__DIR__.'/../templates'],
            'patterns' => [
                'expression_in_text' => '%s',
            ],
        ]);
    }

    protected function enableJsPhpize()
    {
        $compiler = $this->compiler;

        $compiler = new Compiler([
            'paths'    => [__DIR__.'/../templates'],
            'patterns' => [
                'expression_in_text'   => '%s',
                'transform_expression' => function ($jsCode) use (&$compiler) {
                    /** @var JsPhpize $jsPhpize */
                    $jsPhpize = $compiler->getOption('jsphpize_engine');

                    try {
                        return rtrim(trim(preg_replace(
                            '/\{\s*\}$/',
                            '',
                            trim($jsPhpize->compile($jsCode))
                        )), ';');
                    } catch (Exception $exception) {
                        if ($exception instanceof \JsPhpize\Lexer\Exception ||
                            $exception instanceof \JsPhpize\Parser\Exception ||
                            $exception instanceof \JsPhpize\Compiler\Exception
                        ) {
                            return $jsCode;
                        }

                        throw $exception;
                    }
                },
            ],
        ]);

        $compiler->attach(CompilerEvent::COMPILE, function () use ($compiler) {
            $compiler->setOption('jsphpize_engine', new JsPhpize([
                'catchDependencies' => true,
            ]));
        });

        $compiler->attach(CompilerEvent::OUTPUT, function (Compiler\Event\OutputEvent $event) use ($compiler) {

            /** @var JsPhpize $jsPhpize */
            $jsPhpize = $compiler->getOption('jsphpize_engine');
            $dependencies = $jsPhpize->compileDependencies();
            if ($dependencies !== '') {
                $event->setOutput($compiler->getFormatter()->handleCode($dependencies).$event->getOutput());
            }
            $jsPhpize->flushDependencies();
            $compiler->unsetOption('jsphpize_engine');
        });

        $this->compiler = $compiler;
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

    protected function getRenderedHtml($php, array $variables = [])
    {
        if (getenv('LOG_COMPILE')) {
            file_put_contents('temp.php', $php);
        }
        extract($variables);
        ob_start();
        eval('?>'.$php);
        $actual = ob_get_contents();
        ob_end_clean();

        return $actual;
    }

    protected function render($actual, array $options = [], array $variables = [])
    {
        $compiler = $this->compiler;
        $compiler->setOptionsRecursive($options);
        $php = $compiler->compile($this->implodeLines($actual));

        return $this->getRenderedHtml($php, $variables);
    }

    protected function assertRender($expected, $actual, array $options = [], array $variables = [])
    {
        $actual = $this->render($actual, $options, $variables);

        return $this->assertSameLines($expected, $actual);
    }

    protected function assertRenderFile($expected, $actual, array $options = [])
    {
        $compiler = $this->compiler;
        $compiler->setOptionsRecursive($options);
        $php = $compiler->compileFile($actual);
        $actual = $this->getRenderedHtml($php);

        return $this->assertSameLines($expected, $actual);
    }
}
