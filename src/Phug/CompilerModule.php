<?php

namespace Phug;

use Phug\Util\AbstractModule;
use Phug\Util\ModulesContainerInterface;

class CompilerModule extends AbstractModule implements CompilerModuleInterface
{
    public function injectCompiler(Compiler $compiler)
    {
        return $compiler;
    }

    public function plug(ModulesContainerInterface $parent)
    {
        parent::plug($this->injectCompiler($parent));
    }
}
