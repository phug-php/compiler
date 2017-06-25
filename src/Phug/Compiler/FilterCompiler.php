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

        if ($node->getImport()) {
            return null;
        }

        /**
         * @var FilterNode $node
         */
        $name = $node->getName();
        $filters = $this->getCompiler()->getOption('filters');

        $text = $this->compileText($name, $node->getChildren(), $parent, 0);
        $names = explode(':', $name);

        while ($name = array_pop($names)) {
            if (!isset($filters[$name])) {
                throw new CompilerException(
                    'Unknown filter '.$name.'.'
                );
            }

            $options = [];
            foreach ($node->getAttributes() as $attribute) {
                $__pug_eval_attribute = $attribute->getValue();
                $options[$attribute->getName()] = call_user_func(function () use ($__pug_eval_attribute) {
                    return eval('return '.$__pug_eval_attribute.';');
                });
            }

            $text = $this->proceedFilter(
                $filters[$name],
                $text,
                $options
            );
        }

        return new TextElement($text);
    }

    public function proceedFilter($filter, $input, $options)
    {
        if (!is_callable($filter) && class_exists($filter)) {
            $filter = new $filter();
        }

        if (is_object($filter) && method_exists($filter, 'parse')) {
            $filter = [$filter, 'parse'];
        }

        return strval(call_user_func(
            $filter,
            $input,
            $options
        ));
    }
}
