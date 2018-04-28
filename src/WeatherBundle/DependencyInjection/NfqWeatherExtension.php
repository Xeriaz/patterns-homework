<?php

namespace Nfq\WeatherBundle\DependencyInjection;

use Nfq\WeatherBundle\WeatherProviderException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Nfq\WeatherBundle\WeatherProviderInterface;

class NfqWeatherExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('providers.yaml');

        if ($config['provider'] === 'yahoo') {
            $container->setAlias(WeatherProviderInterface::class, 'nfq_weather.provider.yahoo');
        } else if ($config['provider'] === 'openweathermap') {
            $container->setAlias(WeatherProviderInterface::class, 'nfq_weather.provider.openweathermap');
        } else if ($config['provider'] === 'delegating') {
//            $container->setAlias(WeatherProviderInterface::class, 'nfq_weather.provider.delegating');
        } else {
            throw new WeatherProviderException("Provider isn't found");
        }
    }
}
