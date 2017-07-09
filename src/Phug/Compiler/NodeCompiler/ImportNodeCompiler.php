<?php

namespace Phug\Compiler\NodeCompiler;

use Phug\Compiler\AbstractNodeCompiler;
use Phug\Compiler\Layout;
use Phug\CompilerException;
use Phug\Formatter\Element\DocumentElement;
use Phug\Formatter\Element\TextElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\FilterNode;
use Phug\Parser\Node\ImportNode;
use Phug\Parser\Node\TextNode;
use Phug\Parser\NodeInterface;

class ImportNodeCompiler extends AbstractNodeCompiler
{
    protected function getBaseDirectoryForPath($path, $node)
    {
        $compiler = $this->getCompiler();

        if (mb_substr($path, 0, 1) === '/') {
            $base = $compiler->getOption('basedir');
            if (!$base) {
                $this->getCompiler()->throwException(
                    'The "basedir" option is required to use '.
                    'includes and extends with "absolute" paths.',
                    $node
                );
            }

            return $base;
        }

        $base = $compiler->getFileName();
        if (!$base) {
            $this->getCompiler()->throwException(
                'No source file path provided to get relative paths from it.',
                $node
            );
        }

        return dirname($base);
    }

    protected function resolvePath($path, $node)
    {
        $base = $this->getBaseDirectoryForPath($path, $node);
        $file = rtrim($base, '\\/').DIRECTORY_SEPARATOR.ltrim($path, '\\/');

        foreach ($this->getCompiler()->getOption('extensions') as $extension) {
            if (file_exists($file.$extension)) {
                return $file.$extension;
            }
        }

        $this->getCompiler()->throwException(
            'File not found at path '.var_export($path, true).'.',
            $node
        );
    }

    protected function isRawTextFile($path)
    {
        foreach ($this->getCompiler()->getOption('extensions') as $extension) {
            if ($extension === '' && mb_strpos(basename($path), '.') === false) {
                return false;
            }

            if (mb_substr($path, -mb_strlen($extension)) === $extension) {
                return false;
            }
        }

        return true;
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
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to import compiler.',
                $node
            );
        }

        $compiler = $this->getCompiler();
        $path = $this->resolvePath($node->getPath(), $node);

        /** @var FilterNode $filter */
        if ($filter = $node->getFilter()) {
            $text = new TextNode();
            $text->setValue(file_get_contents($path));
            $filter->appendChild($text);
            $import = $filter->getImport();
            $filter->setImport(null);
            $element = $compiler->compileNode($filter, $parent);
            $filter->setImport($import);

            return $element;
        }

        if ($this->isRawTextFile($path)) {
            return new TextElement(file_get_contents($path), $node);
        }

        $subCompiler = clone $compiler;
        $subCompiler->setImportNode($node);
        $element = $subCompiler->compileFileIntoElement($path);
        $compiler->importBlocks($subCompiler->getBlocks());
        $isIncludeImport = $node->getName() === 'include';

        if ($layout = $subCompiler->getLayout()) {
            $element = $layout->getDocument();
            $layoutCompiler = $layout->getCompiler();
            if ($isIncludeImport) {
                $layoutCompiler->compileBlocks();
            }
        }

        if (!$subCompiler->isImportNodeYielded()) {
            $yield = $element;
            if ($yield instanceof DocumentElement && $yield->getChildCount()) {
                $yield = $yield->getChildAt($yield->getChildCount() - 1);
            }
            $this->compileNodeChildren($node, $yield);
        }

        if ($isIncludeImport) {
            return $element;
        }

        if ($node->getName() === 'extend') {
            $compiler->setLayout(new Layout($element, $subCompiler));
        }

        return null;
    }
}
