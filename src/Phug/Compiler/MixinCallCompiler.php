<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\CodeElement;
use Phug\Formatter\Element\DocumentElement;
use Phug\Formatter\Element\ExpressionElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\AttributeNode;
use Phug\Parser\Node\MixinCallNode;
use Phug\Parser\Node\MixinNode;
use Phug\Parser\NodeInterface;

class MixinCallCompiler extends AbstractNodeCompiler
{
    public function compileNode(NodeInterface $node, ElementInterface $parent = null)
    {
        if (!($node instanceof MixinCallNode)) {
            throw new CompilerException(
                'Unexpected '.get_class($node).' given to mixin call compiler.'
            );
        }

        /**
         * @var MixinCallNode $node
         */
        $name = $node->getName();
        $compiler = $this->getCompiler();
        $mixins = $compiler->getMixins();
        /**
         * @var MixinNode $declaration
         */
        $declaration = $mixins->findFirstByName($name);
        if (!$declaration) {
            throw new CompilerException(
                'Unknown '.$name.' mixin called.'
            );
        }
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
        $scope = new ExpressionElement(var_export(array_keys($variables), true));
        $scopeName = 'scope_'.spl_object_hash($node);
        $document = new DocumentElement();
        $document->appendChild($this->createVariable($scopeName, $scope));
        foreach ($variables as $name => $value) {
            $document->appendChild($this->createVariable($name, $value));
        }
        $mixinBlocks = $compiler->getMixinBlocks();
        echo spl_object_hash($mixinBlocks).' : '.$mixinBlocks->count()."\n\n";
        if ($mixinBlocks->offsetExists($declaration)) {
            $block = $mixinBlocks->offsetGet($declaration);
            $compiler->replaceBlock($block, $node->getChildren());
        }
        $this->compileNodeChildren($declaration, $document);
        $document->appendChild(new CodeElement('extract($'.$scopeName.')'));

        return $document;
    }
}
