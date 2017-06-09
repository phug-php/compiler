<?php

namespace Phug;

use Phug\Util\ModuleInterface;

interface CompilerModuleInterface extends ModuleInterface
{
    public function injectCompiler(Compiler $compiler);
}
