<?php

namespace Orbeji\UnusedRoutes;

use Orbeji\UnusedRoutes\DependencyInjection\UnusedRoutesExtension;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class UnusedRoutesBundle extends Bundle
{
//    public function configure(DefinitionConfigurator $definition) : void
//    {
//        $definition->import('../config/definition.php');
//    }
//
//    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder) : void
//    {
//        $container->import('../config/services.php');
//    }
    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new UnusedRoutesExtension();
    }
}