<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\Ast\NodeInterface;
use Phug\CompilerException;
use Phug\Formatter\Element\CodeElement;
use Phug\Formatter\Element\DocumentElement;
use Phug\Formatter\Element\ExpressionElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\AttributeNode;
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

    public function compileNode(ParserNodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof MixinCallNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to mixin call compiler.'
            );
        }

        /** @var MixinCallNode $node */
        $mixinName = $node->getName();
        $compiler = $this->getCompiler();
        /** @var MixinNode $declaration */
        $declaration = $compiler->requireMixin($mixinName);
        $arguments = [];
        $attributes = [];
        foreach ($node->getAttributes() as $attribute) {
            $store = is_null($attribute->getName()) ? 'arguments' : 'attributes';
            array_push($$store, $attribute);
        }
        $attributes = '['.implode(', ', array_map(function (AttributeNode $attribute) {
            return var_export($attribute->getName(), true).' => '.$attribute->getValue();
        }, $attributes)).']';
        $variables = [
            'attributes' => new ExpressionElement($attributes),
        ];
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
        $this->proceedBlocks($document, $this->getCompiledChildren($node, $parent));
        $document->appendChild(new CodeElement('extract($'.$scopeName.')'));

        return $document;
    }
}
