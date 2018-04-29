<?php

namespace Nfq\WeatherBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Nfq\WeatherBundle\WeatherProviderInterface;
use Symfony\Component\DependencyInjection\Reference;

class NfqWeatherExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('providers.yaml');

        $providerIdPrefix = 'nfq_weather.provider.';

        if (isset($config['providers']['openweathermap']['api_key'])) {
            $container->getDefinition($providerIdPrefix.'openweathermap')
                ->replaceArgument(0, $config['providers']['openweathermap']['api_key']);
        }

        $providerId =  $providerIdPrefix . $config['provider'];

        foreach ($config['providers']['delegating']['providers'] as $provider){
            $providerReferences[] = new Reference($providerIdPrefix.$provider);
        }

        if (isset($config['providers']['delegating']['providers'])) {
            $container->getDefinition($providerIdPrefix.'delegating')
                ->replaceArgument(0, $providerReferences);
        }

        $container->setAlias(WeatherProviderInterface::class, $providerId);
    }
}
