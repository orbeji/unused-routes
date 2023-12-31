<?php

namespace Orbeji\UnusedRoutes\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('unused_routes');

        $treeBuilder
            ->getRootNode()
                ->children()
                    ->scalarNode('file_path')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}