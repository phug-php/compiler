<?php

namespace Phug\Compiler\Locator;

use Phug\Compiler\LocatorInterface;

class FileLocator implements LocatorInterface
{
    private function normalize($path)
    {
        return rtrim(str_replace('\\', '/', $path), '/'); //Windows can handle both, so we don't care about backslashes
    }

    public function locate($path, array $locations, array $extensions)
    {
        if (is_file($path)) {
            return is_readable($path) ? $path : null;
        }

        $path = $this->normalize($path);

        foreach ($locations as $location) {
            $location = $this->normalize($location);

            foreach ($extensions as $extension) {
                $fullPath = "$location/$path$extension";

                if (is_file($fullPath) && is_readable($fullPath)) {
                    return $fullPath;
                }
            }
        }

        return null;
    }
}
