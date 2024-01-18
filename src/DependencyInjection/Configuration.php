<?php declare(strict_types=1);

namespace Orbeji\UnusedRoutes\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Webmozart\Assert\Assert;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('unused_routes');

        $rootNode = $treeBuilder->getRootNode();
        Assert::isInstanceOf($rootNode, ArrayNodeDefinition::class);

        $rootNode
            ->children()
            ->scalarNode('file_path')
            ->defaultValue('%kernel.logs_dir%')
            ->end()
            ->scalarNode('file_name')
            ->defaultValue('accessed_routes.log')
            ->end()
            ->end();

        return $treeBuilder;
    }
}
