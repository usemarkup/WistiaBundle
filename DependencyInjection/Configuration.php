<?php

namespace Markup\WistiaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('markup_wistia');

        $rootNode
            ->children()
            ->scalarNode('api_key')
                ->defaultNull()
            ->end()
            ->integerNode('timeout')
                ->defaultValue(5)->min(0)->max(30)
            ->end()
            ->integerNode('connect_timeout')
                ->defaultValue(3)->min(0)->max(30)
            ->end()
            ->scalarNode('cache')
                ->defaultNull()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
