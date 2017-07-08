<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\Formatter\Element\DocumentElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\DocumentNode;
use Phug\Parser\NodeInterface;

class DocumentCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof DocumentNode)) {
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to document compiler.',
                $node
            );
        }

        $document = new DocumentElement($node);

        $this->compileNodeChildren($node, $document);

        return $document;
    }
}
