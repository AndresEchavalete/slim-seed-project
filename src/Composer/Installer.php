<?php

namespace SlimSeed\Composer;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;

class Installer extends LibraryInstaller
{
    public function supports($packageType): bool
    {
        return $packageType === 'slimseed-framework';
    }

    public function getInstallPath(PackageInterface $package): string
    {
        return 'vendor/slimseed/framework';
    }
}
