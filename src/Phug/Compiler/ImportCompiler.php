<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\MarkupElement;
use Phug\Parser\Node\ImportNode;
use Phug\Parser\NodeInterface;

class ImportCompiler extends AbstractNodeCompiler
{
    protected function getBaseDirectoryForPath($path)
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

            return $base;
        }

        $base = $compiler->getFileName();
        if (!$base) {
            throw new CompilerException(
                'No source file path provided to get relative paths from it.'
            );
        }

        return $base;
    }

    protected function resolvePath($path)
    {
        $base = $this->getBaseDirectoryForPath($path);
        $file = rtrim($base, '\\/').DIRECTORY_SEPARATOR.ltrim($path, '\\/');

        if (!file_exists($file)) {
            throw new CompilerException(
                'file not found at path '.var_export($path, true).'.'
            );
        }

        return $file;
    }

    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof ImportNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to import compiler.'
            );
        }

        $compiler = clone $this->getCompiler();
        $element = $compiler->compileFileIntoElement($this->resolvePath($node->getPath()));

        if ($node->getName() === 'include') {
            return $element;
        }

        if ($node->getName() === 'extends') {
            $this->setLayout(new Layout($element, $compiler));
        }
    }
}
