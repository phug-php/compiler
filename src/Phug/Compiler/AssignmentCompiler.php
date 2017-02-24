<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\ExpressionElement;
use Phug\Parser\Node\AssignmentNode;
use Phug\Parser\NodeInterface;

class AssignmentCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node)
    {
        if (!($node instanceof AssignmentNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to assignment compiler.'
            );
        }

        /**
         * @var AssignmentNode $node
         */
        $name = $node->getName();

        return new ExpressionElement(
            'foreach (array_merge('.$name.') as $name => $value) {'."\n".
            '  if ($value) {'."\n".
            '    echo  $name;'."\n".
            '    echo "=\"$value\"";'."\n".
            '  }'."\n".
            '}'
        );
    }
}
