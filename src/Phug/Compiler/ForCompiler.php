<?php

namespace Phug\Compiler;

use Phug\CompilerException;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\ForNode;
use Phug\Parser\NodeInterface;

class ForCompiler extends EachCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof ForNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to for compiler.'
            );
        }

        /**
         * @var ForNode $node
         */
        $subject = $node->getSubject();
        if (preg_match('/^
                \s*\$?(?P<item>[a-z_\x7f-\xff][a-z0-9_\x7f-\xff]*)
                (?:
                    \s*,\s*
                    \$?(?P<key>[a-z_\x7f-\xff][a-z0-9_\x7f-\xff]*)
                )?
                \s*(?P<operator>(?:of|in))
                (?P<subject>.*)
            $/ix', $subject, $matches)
        ) {
            $key = empty($matches['key']) ? null : $matches['key'];
            $item = $matches['item'];
            $subject = trim($matches['subject']);
            if (strtolower($matches['operator']) === 'of') {
                $swap = $item;
                $item = $key;
                if ($item === null) {
                    $item = '__none';
                }
                $key = $swap;
            }

            return $this->compileLoop($node, $subject, $key, $item);
        }

        return $this->wrapStatement($node, 'for', $subject);
    }
}
