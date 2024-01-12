<?php

namespace Orbeji\UnusedRoutes\DependencyInjection;

use Exception;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Webmozart\Assert\Assert;

final class UnusedRoutesExtension extends Extension implements ConfigurationInterface
{
    /**
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('unused_routes.file_path', $config['file_path']);
        $container->setParameter('unused_routes.file_name', $config['file_name']);
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ?ConfigurationInterface
    {
        return $this;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('unused_routes');

        $rootNode = $treeBuilder->getRootNode();
        Assert::isInstanceOf($rootNode, ArrayNodeDefinition::class);

        $rootNode
            ->children()
                ->scalarNode('file_path')
                    ->defaultValue('/var/log')
                ->end()
                ->scalarNode('file_name')
                    ->defaultValue('accessed_routes.log')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}