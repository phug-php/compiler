<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\CodeElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\WhenNode;
use Phug\Parser\NodeInterface;

class WhenCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof WhenNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to when compiler.'
            );
        }

        /**
         * @var WhenNode $node
         */
        $value = $node->getSubject();

        if ($value === null) {
            $parent->appendChild(new CodeElement('default:'));
            $this->compileNodeChildren($node, $parent);

            return null;
        }

        $parent->appendChild(new CodeElement('case '.$value.':'));
        if ($node->hasChildren()) {
            $this->compileNodeChildren($node, $parent);
            $parent->appendChild(new CodeElement('break;'));
        }

        return null;
    }
}
