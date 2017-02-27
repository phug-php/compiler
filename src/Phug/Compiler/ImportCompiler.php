<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\ImportNode;
use Phug\Parser\NodeInterface;

class ImportCompiler extends AbstractNodeCompiler
{
    protected function getPath($path)
    {
        $compiler = $this->getCompiler();

        if (substr($path, 0, 1) === '/') {
            $base = $compiler->getOption('basedir');
            if (!$base) {
                throw new CompilerException(
                    'The "basedir" option is required to use '.
                    'includes and extends with "absolute" paths.'
                );
            }
            $file = rtrim($base, '\\/').DIRECTORY_SEPARATOR.ltrim($path, '\\/');
            if (!file_exists($file)) {
                throw new CompilerException(
                    'file not found at path '.var_export($path, true).'.'
                );
            }

            return $file;
        }

        $base = $compiler->getFileName();
        if (!$base) {
            throw new CompilerException(
                'No source file path provided to get relative paths from it.'
            );
        }
    }

    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof ImportNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to import compiler.'
            );
        }

        if ($node->getName() === 'include') {
            $compiler = $this->getCompiler();
            if ($compiler->getOption('base_dir')) {

            }
        }

        return new MarkupElement('to-do-import');
    }
}
