<?php

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition): void {
    $definition->rootNode()
        ->children()
        ->scalarNode('file_path')->defaultValue('/var/log/unused_routes.log')->end()
        ->end()
    ;
};