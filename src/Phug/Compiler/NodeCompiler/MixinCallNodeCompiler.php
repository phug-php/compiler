<?php

namespace Phug\Compiler\NodeCompiler;

use Phug\Ast\NodeInterface;
use Phug\Compiler\AbstractNodeCompiler;
use Phug\Compiler\Element\BlockElement;
use Phug\Compiler\Util\PhpUnwrap;
use Phug\Formatter\Element\AttributeElement;
use Phug\Formatter\Element\CodeElement;
use Phug\Formatter\Element\DocumentElement;
use Phug\Formatter\Element\ExpressionElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\CodeNode;
use Phug\Parser\Node\MixinCallNode;
use Phug\Parser\Node\MixinNode;
use Phug\Parser\NodeInterface as ParserNodeInterface;

class MixinCallNodeCompiler extends AbstractNodeCompiler
{
    protected function proceedBlocks(NodeInterface $node, array $children)
    {
        if ($node instanceof BlockElement) {
            $this->getCompiler()->replaceBlock($node, array_map(function ($child) {
                return $child instanceof BlockElement ? $child : clone $child;
            }, $children));

            return;
        }

        foreach ($node->getChildren() as $childNode) {
            $this->proceedBlocks($childNode, $children);
        }
    }

    protected function compileDynamicMixin($mixinName, ParserNodeInterface $node, $attributes, array $arguments)
    {
        $compiler = $this->getCompiler()->enableDynamicMixins();
        $formatter = $compiler->getFormatter();
        $name = is_string($mixinName)
            ? var_export($mixinName, true)
            : $formatter->formatCode($compiler->compileNode($mixinName)->getValue());
        $children = new PhpUnwrap($this->getCompiledChildren($node, new DocumentElement($node)), $formatter);
        $call = new CodeElement(
            implode("\n", [
                'if (!isset($__pug_mixins)) {',
                '    $__pug_mixins = [];',
                '}',
                '$__pug_vars = [\'__pug_mixins\' => $__pug_mixins];',
                'foreach (array_keys(get_defined_vars()) as $key) {',
                '    if ('.
                        'mb_substr($key, 0, 6) === \'__pug_\' || '.
                        'in_array($key, [\'attributes\', \'arguments\'])'.
                    ') {',
                '        continue;',
                '    }',
                '    $ref = &$GLOBALS[$key];',
                '    $value = &$$key;',
                '    if($ref !== $value){',
                '        $__pug_vars[$key] = &$value;',

                '        continue;',
                '    }',
                '    $savedValue = $value;',
                '    $value = ($value === true) ? false : true;',
                '    $isGlobalReference = ($value === $ref);',
                '    $value = $savedValue;',

                '    if (!$isGlobalReference) {',
                '        $__pug_vars[$key] = &$value;',
                '    }',
                '}',
                '$__pug_mixins['.$name.'](['.
                    '"attributes" => '.($attributes
                        ? $formatter->formatCode($attributes->getValue())
                        : '[]'
                    ).','.
                    '"arguments" => ['.implode(', ', array_map(function ($argument) use ($formatter) {
                        /* @var AttributeElement $argument */
                        return $formatter->formatCode($argument->getValue());
                    }, $arguments)).'],'.
                    '"children" => function ($__pug_children_vars, &$__pug_mixins) use ($__pug_children) {'."\n".
                    '    foreach ($__pug_children_vars as $key => &$value) {'."\n".
                    '        $$key = &$value;'."\n".
                    '    }'."\n".
                    '    '.$children."\n".
                    '},'.
                    '"globals" => $__pug_vars,'.
                ']);',
            ]),
            $node
        );

        $call->preventFromTransformation();

        return $call;
    }

    protected function isInsideMixinNode(NodeInterface $node)
    {
        for ($parent = $node->getParent(); $parent; $parent = $parent->getParent()) {
            if ($parent instanceof MixinNode) {
                return true;
            }
        }

        return false;
    }

    public function compileNode(ParserNodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof MixinCallNode)) {
            $this->getCompiler()->throwException(
                'Unexpected '.get_class($node).' given to mixin call compiler.',
                $node
            );
        }

        /** @var MixinCallNode $node */
        $mixinName = $node->getName();
        $compiler = $this->getCompiler();
        $arguments = [];
        $attributes = [];
        foreach ($node->getAttributes() as $attribute) {
            $store = is_null($attribute->getName()) ? 'arguments' : 'attributes';
            array_push($$store, $attribute);
        }
        $variables = [
            'attributes' => count($attributes)
                ? $compiler->getFormatter()->formatAttributesList(array_map(function ($node) use ($compiler) {
                    return $compiler->compileNode($node);
                }, $attributes))
                : new ExpressionElement('[]', $node),
        ];
        $mergeAttributes = [];
        foreach ($node->getAssignments() as $assignment) {
            if ($assignment->getName() === 'attributes') {
                foreach ($assignment->getAttributes() as $attribute) {
                    /* @var AttributeElement $attribute */
                    $mergeAttributes[] = $compiler->getFormatter()->formatCode($attribute->getValue());
                }
            }
        }
        if (count($mergeAttributes)) {
            $variables['attributes']->setValue(sprintf(
                'array_merge(%s, %s)',
                $variables['attributes']->getValue(),
                implode(', ', $mergeAttributes)
            ));
        }
        if ($compiler->getOption('dynamic_mixins') ||
            !is_string($mixinName) ||
            $compiler->isDynamicMixinsEnabled() ||
            $this->isInsideMixinNode($node)
        ) {
            return $this->compileDynamicMixin($mixinName, $node, $variables['attributes'], $arguments);
        }
        /** @var MixinNode $declaration */
        $declaration = $compiler->requireMixin($mixinName, $node);
        foreach ($declaration->getAttributes() as $index => $attribute) {
            $name = $attribute->getName();
            if (mb_substr($name, 0, 3) === '...') {
                $name = mb_substr($name, 3);
                if (mb_substr($name, 0, 1) === '$') {
                    $name = mb_substr($name, 1);
                }
                $value = [];
                foreach (array_slice($arguments, $index) as $subIndex => $argument) {
                    $value[] = isset($arguments[$index + $subIndex])
                        ? $arguments[$index + $subIndex]->getValue()
                        : 'null';
                }
                $variables[$name] = new ExpressionElement(
                    '['.implode(', ', $value).']',
                    $node
                );
                break;
            }
            if (mb_substr($name, 0, 1) === '$') {
                $name = mb_substr($name, 1);
            }
            $variables[$name] = new ExpressionElement(
                isset($arguments[$index])
                    ? $arguments[$index]->getValue()
                    : 'null',
                $node
            );
        }
        $scope = new ExpressionElement(sprintf(
            'compact(%s)',
            var_export(array_keys($variables), true)
        ), $node);
        $scopeName = 'scope_'.spl_object_hash($node);
        $document = new DocumentElement($node);
        $document->appendChild($this->createVariable($node, $scopeName, $scope));
        foreach ($variables as $name => $value) {
            $document->appendChild($this->createVariable($node, $name, $value));
        }
        foreach ($declaration->getChildren() as $child) {
            $document->appendChild(clone $child);
        }
        $node->appendChild(new CodeNode(null, $node->getSourceLocation(), 'extract($'.$scopeName.')'));
        $this->proceedBlocks($document, $this->getCompiledChildren($node, $parent));

        return $document;
    }
}
