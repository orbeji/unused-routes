<?php

declare(strict_types=1);

namespace Orbeji\UnusedRoutes;

use Orbeji\UnusedRoutes\DependencyInjection\UnusedRoutesExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class UnusedRoutesBundle extends Bundle
{
    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new UnusedRoutesExtension();
    }
}
