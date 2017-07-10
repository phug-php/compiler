<?php

namespace Phug\Compiler\Event;

use Phug\CompilerEvent;
use Phug\Event;
use Phug\Formatter\ElementInterface;

class ElementEvent extends Event
{
    private $element;

    /**
     * ElementEvent constructor.
     *
     * @param ElementInterface $element
     */
    public function __construct(ElementInterface $element)
    {
        parent::__construct(CompilerEvent::ELEMENT);

        $this->element = $element;
    }

    /**
     * @return ElementInterface
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param ElementInterface $element
     */
    public function setElement($element)
    {
        $this->element = $element;
    }
}
