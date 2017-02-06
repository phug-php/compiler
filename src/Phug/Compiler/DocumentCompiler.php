<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\DocumentElement;
use Phug\Parser\Node\DocumentNode;
use Phug\Parser\NodeInterface;

class DocumentCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof DocumentNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to document compiler.'
            );
        }

        $document = new DocumentElement();

        $this->compileNodeChildren($node, $document);

        return $document;
    }
}
