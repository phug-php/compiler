<?php

namespace Phug\Compiler;

interface LocatorInterface
{

    public function locate($path, array $locations, array $extensions);
}
