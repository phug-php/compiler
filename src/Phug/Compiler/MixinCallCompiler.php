<?php

namespace Phug\Compiler;

use Phug\AbstractNodeCompiler;
use Phug\CompilerException;
use Phug\Formatter\Element\CodeElement;
use Phug\Formatter\Element\DocumentElement;
use Phug\Formatter\Element\ExpressionElement;
use Phug\Formatter\ElementInterface;
use Phug\Parser\Node\MixinCallNode;
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
        $declaration = $mixins->findFirstByName($name);
        if (!$declaration) {
            throw new CompilerException(
                'Unknown '.$name.' mixin called.'
            );
        }
        $arguments = [];
        $atttributes = [];
        foreach ($node->getAttributes() as $attribute) {
            $store = is_null($attribute->getName()) ? 'arguments' : 'atttributes';
            $$store[] = $attribute;
        }
        $attributes = '['.implode(', ', array_map(function ($attribute) {
            return var_export($attribute->getName(), true).' => '.$attribute->getValue();
        }, $atttributes)).']';
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
        // $block = $compiler->getMixinBlock($name);
        // $block->proceedNodeChildren($declaration, 'replace');
        $this->compileNodeChildren($declaration, $document);
        $document->appendChild(new CodeElement('extract($'.$scopeName.')'));

        return $document;
    }
}
