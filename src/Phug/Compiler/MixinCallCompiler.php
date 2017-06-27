<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\Ast\NodeInterface;
use Phug\CompilerException;
use Phug\Formatter\Element\DocumentElement;
use Phug\Formatter\Element\ExpressionElement;
use Phug\Formatter\Element\TextElement;
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
            $this->getCompiler()->replaceBlock($node, $children);

            return;
        }

        foreach ($node->getChildren() as $childNode) {
            $this->proceedBlocks($childNode, $children);
        }
    }

    protected function compileDynamicMixin($mixinName, ParserNodeInterface $node, ElementInterface $parent)
    {
        return new TextElement('Dynamic mixin names not yet supported.');
    }

    public function compileNode(ParserNodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof MixinCallNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to mixin call compiler.'
            );
        }

        /** @var MixinCallNode $node */
        $mixinName = $node->getName();
        if (!is_string($mixinName)) {
            return $this->compileDynamicMixin($mixinName, $node, $parent);
        }
        $compiler = $this->getCompiler();
        /** @var MixinNode $declaration */
        $declaration = $compiler->requireMixin($mixinName);
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
                : new ExpressionElement('[]'),
        ];
        $mergeAttributes = [];
        foreach ($node->getAssignments() as $assignment) {
            if ($assignment->getName() === 'attributes') {
                foreach ($assignment->getAttributes() as $attribute) {
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
        foreach ($declaration->getAttributes() as $index => $attribute) {
            $variables[$attribute->getName()] = new ExpressionElement(
                isset($arguments[$index]) ? $arguments[$index]->getValue() : 'null'
            );
        }
        $scope = new ExpressionElement(sprintf(
            'compact(%s)',
            var_export(array_keys($variables), true)
        ));
        $scopeName = 'scope_'.spl_object_hash($node);
        $document = new DocumentElement();
        $document->appendChild($this->createVariable($scopeName, $scope));
        foreach ($variables as $name => $value) {
            $document->appendChild($this->createVariable($name, $value));
        }
        foreach ($declaration->getChildren() as $child) {
            $document->appendChild(clone $child);
        }
        $node->appendChild(new CodeNode('extract($'.$scopeName.')'));
        $this->proceedBlocks($document, $this->getCompiledChildren($node, $parent));

        return $document;
    }
}
