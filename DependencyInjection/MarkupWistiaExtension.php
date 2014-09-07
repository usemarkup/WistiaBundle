<?php

namespace Markup\WistiaBundle\DependencyInjection;

use Markup\JobQueueBundle\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MarkupWistiaExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->loadApiKey($config, $container);
        $this->loadCache($config, $container);
        $this->setTimeout($config, $container);
    }

    private function loadApiKey(array $config, ContainerBuilder $container)
    {
        if (!isset($config['api_key'])) {
            throw new InvalidConfigurationException('Api key must be set to use Wistia Data API');
        }
        $definition = $container->getDefinition('markup_wistia');
        $definition->addMethodCall('setApiKey', [$config['api_key']]);
    }

    private function loadCache(array $config, ContainerBuilder $container)
    {
        if (!isset($config['cache']) || !$config['cache']) {
            return;
        }
        $cache = new Reference($config['cache']);
        $definition = $container->getDefinition('markup_wistia');
        $definition->addMethodCall('setCache', [$cache]);
    }

    private function setTimeout(array $config, ContainerBuilder $container)
    {
        $definition = $container->getDefinition('markup_wistia');
        $definition->addMethodCall('setTimeout', [$config['timeout']]);
        $definition->addMethodCall('setConnectTimeout', [$config['connect_timeout']]);
    }
}
