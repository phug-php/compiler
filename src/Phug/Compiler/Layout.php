<?php

namespace Phug\Compiler;

use Phug\Compiler;
use Phug\Formatter\Element\DocumentElement;

class Layout
{
    /**
     * @var DocumentElement
     */
    private $document;

    /**
     * @var Compiler
     */
    private $compiler;

    public function __construct(DocumentElement $document, Compiler $compiler)
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
