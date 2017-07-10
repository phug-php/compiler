<?php

namespace Phug\Compiler\Event;

use Phug\CompilerEvent;
use Phug\Event;

class OutputEvent extends Event
{

    private $output;

    /**
     * OutputEvent constructor.
     *
     * @param string $output
     */
    public function __construct($output)
    {
        parent::__construct(CompilerEvent::OUTPUT);

        $this->output = $output;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param string $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }
}
