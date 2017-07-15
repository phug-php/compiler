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
        $currentPath = dirname($compiler->getPath());

        if ($currentPath) {
            $compiler->pushPath($currentPath);
        }

        $path = $compiler->resolve($node->getPath());

        if ($currentPath) {
            $compiler->popPath();
        }

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

        $ext = pathinfo($path, PATHINFO_EXTENSION) ?: '';
        $exts = $compiler->getOption('extensions');

        if (!in_array($ext === '' ? '' : ".$ext", $exts, true)) {
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
