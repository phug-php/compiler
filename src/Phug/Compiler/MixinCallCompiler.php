<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\Ast\NodeInterface;
use Phug\CompilerException;
use Phug\Formatter\Element\AttributeElement;
use Phug\Formatter\Element\CodeElement;
use Phug\Formatter\Element\DocumentElement;
use Phug\Formatter\Element\ExpressionElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\CodeNode;
use Phug\Parser\Node\MixinCallNode;
use Phug\Parser\Node\MixinNode;
use Phug\Parser\NodeInterface as ParserNodeInterface;

class MixinCallCompiler extends AbstractNodeCompiler
{
    protected function proceedBlocks(NodeInterface $node, array $children)
    {
        if ($node instanceof Block) {
            $this->getCompiler()->replaceBlock($node, array_map(function ($child) {
                return $child instanceof Block ? $child : clone $child;
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
        /* @var ExpressionElement $expression */
        $expression = $compiler->compileNode($mixinName);
        $children = implode('', array_map(function ($child) use ($formatter) {
            return $formatter->format($child);
        }, $this->getCompiledChildren($node, new DocumentElement($node))));
        $call = new CodeElement(
            $node,
            implode("\n", [
                '$__pug_vars = [];',
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
                '$__pug_mixins['.
                $formatter->formatCode($expression->getValue()).
                '](['.
                    '"attributes" => '.($attributes
                        ? $formatter->formatCode($attributes->getValue())
                        : '[]'
                    ).','.
                    '"arguments" => ['.implode(', ', array_map(function ($argument) use ($formatter) {
                        /* @var AttributeElement $argument */
                        return $formatter->formatCode($argument->getValue());
                    }, $arguments)).'],'.
                    '"children" => '.var_export($children, true).','.
                    '"globals" => $__pug_vars,'.
                '])',
            ])
        );

        $call->preventFromTransformation();

        return $call;
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
                : new ExpressionElement($node, '[]'),
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
        if (!is_string($mixinName)) {
            return $this->compileDynamicMixin($mixinName, $node, $variables['attributes'], $arguments);
        }
        /** @var MixinNode $declaration */
        $declaration = $compiler->requireMixin($mixinName, $node);
        foreach ($declaration->getAttributes() as $index => $attribute) {
            $name = $attribute->getName();
            if (substr($name, 0, 3) === '...') {
                $name = substr($name, 3);
                $value = [];
                foreach (array_slice($arguments, $index) as $subIndex => $argument) {
                    $value[] = isset($arguments[$index + $subIndex])
                        ? $arguments[$index + $subIndex]->getValue()
                        : 'null';
                }
                $variables[$name] = new ExpressionElement(
                    $node,
                    '['.implode(', ', $value).']'
                );
                break;
            }
            $variables[$name] = new ExpressionElement(
                $node,
                isset($arguments[$index])
                    ? $arguments[$index]->getValue()
                    : 'null'
            );
        }
        $scope = new ExpressionElement($node, sprintf(
            'compact(%s)',
            var_export(array_keys($variables), true)
        ));
        $scopeName = 'scope_'.spl_object_hash($node);
        $document = new DocumentElement($node);
        $document->appendChild($this->createVariable($node, $scopeName, $scope));
        foreach ($variables as $name => $value) {
            $document->appendChild($this->createVariable($node, $name, $value));
        }
        foreach ($declaration->getChildren() as $child) {
            $document->appendChild(clone $child);
        }
        $node->appendChild(new CodeNode($node, 'extract($'.$scopeName.')'));
        $this->proceedBlocks($document, $this->getCompiledChildren($node, $parent));

        return $document;
    }
}
