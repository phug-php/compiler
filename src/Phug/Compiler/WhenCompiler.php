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
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to when compiler.',
                $node
            );
        }

        /**
         * @var WhenNode $node
         */
        $value = $node->getSubject();

        if ($value === null) {
            $parent->appendChild(new CodeElement($node, 'default:'));
            $this->compileNodeChildren($node, $parent);

            return null;
        }

        $parent->appendChild(new CodeElement($node, 'case '.$value.':'));
        if ($node->hasChildren()) {
            $this->compileNodeChildren($node, $parent);
            $parent->appendChild(new CodeElement($node, 'break;'));
        }

        return null;
    }
}
