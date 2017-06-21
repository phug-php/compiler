<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\TextElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\FilterNode;
use Phug\Parser\Node\TextNode;
use Phug\Parser\NodeInterface;

class FilterCompiler extends AbstractNodeCompiler
{
    protected function compileText($name, $children, $parent, $indentLevel)
    {
        return implode("\n", array_map(function (TextNode $node) use ($name, $indentLevel, $parent) {
            $element = $this->getCompiler()->compileNode($node, $parent);
            if (!($element instanceof TextElement)) {
                throw new CompilerException(
                    'Unexpected '.get_class($element).' in '.$name.' filter.'
                );
            }

            $text = $element->getValue();
            if ($node->hasChildren()) {
                $childrenIndent = $indentLevel + 1;
                $text .=
                    "\n".
                    str_repeat(' ', $childrenIndent * 2).
                    $this->compileText(
                        $name,
                        $node->getChildren(),
                        $parent,
                        $childrenIndent
                    );
            }

            return $text;
        }, $children));
    }

    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof FilterNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to filter compiler.'
            );
        }

        /**
         * @var FilterNode $node
         */
        $name = $node->getName();
        $filters = $this->getCompiler()->getOption('filters');
        if (!isset($filters[$name])) {
            throw new CompilerException(
                'Unknown filter '.$name.'.'
            );
        }

        $text = $this->compileText($name, $node->getChildren(), $parent, 0);
        $options = [];
        foreach ($node->getAttributes() as $attribute) {
            $__pug_eval_attribute = $attribute->getValue();
            $options[$attribute->getName()] = call_user_func(function () use ($__pug_eval_attribute) {
                return eval('return '.$__pug_eval_attribute.';');
            });
        }

        return new TextElement(call_user_func(
            $filters[$name],
            $text,
            $options
        ));
    }
}
