<?php

namespace Phug\Compiler;

use Phug\CompilerInterface;
use Phug\Formatter\Element\DocumentElement;

class Layout
{
    /**
     * @var DocumentElement
     */
    private $document;

    /**
     * @var CompilerInterface
     */
    private $compiler;

    public function __construct(DocumentElement $document, CompilerInterface $compiler)
    {
        $this->document = $document;
        $this->compiler = $compiler;
    }

    public function getCompiler()
    {
        return $this->compiler;
    }

    public function getDocument()
    {
        return $this->document;
    }
}
