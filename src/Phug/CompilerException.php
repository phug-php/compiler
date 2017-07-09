<?php

namespace Phug;

use Phug\Util\Partial\PugFileLocationTrait;
use Phug\Util\PugFileLocationInterface;

/**
 * Represents an exception that is thrown during the compiling process.
 */
class CompilerException extends \Exception implements PugFileLocationInterface
{
    use PugFileLocationTrait;

    public function __construct($message = '', $code = 0, $previous = null, $file = null, $line = null, $offset = null)
    {
        $this->setPugFile($file);
        $this->setPugLine($line);
        $this->setPugOffset($offset);
        parent::__construct($message, intval($code), $previous);
    }
}
