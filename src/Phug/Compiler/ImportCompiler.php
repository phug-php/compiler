<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\ElementInterface;
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

        return dirname($base);
    }

    protected function resolvePath($path)
    {
        $base = $this->getBaseDirectoryForPath($path);
        $file = rtrim($base, '\\/').DIRECTORY_SEPARATOR.ltrim($path, '\\/');

        foreach ($this->getCompiler()->getOption('extensions') as $extension) {
            if (file_exists($file.$extension)) {
                return $file.$extension;
            }
        }

        throw new CompilerException(
            'File not found at path '.var_export($path, true).'.'
        );
    }

    /**
     * @param NodeInterface    $node
     * @param ElementInterface $parent
     *
     * @throws CompilerException
     *
     * @return null|ElementInterface
     */
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof ImportNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to import compiler.'
            );
        }

        $compiler = $this->getCompiler();
        $subCompiler = clone $compiler;
        $path = $this->resolvePath($node->getPath());
        $element = $subCompiler->compileFileIntoElement($path);

        if ($node->getName() === 'include') {
            return $element;
        }

        if ($node->getName() === 'extend') {
            $compiler->setLayout(new Layout($element, $subCompiler));
        }

        return null;
    }
}
