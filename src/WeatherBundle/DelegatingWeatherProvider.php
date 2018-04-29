<?php

namespace Nfq\WeatherBundle;

class DelegatingWeatherProvider implements WeatherProviderInterface
{
    /**
     * @var array
     */
    private $providers = [];

    /**
     * DelegatingWeatherProvider constructor.
     * @param array $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(Location $location): Weather
    {
        foreach ($this->providers as $provider) {
                try {
                    return $provider->fetch($location);
                } catch (WeatherProviderException $e) { }
        }
        throw new WeatherProviderException("Weather providers can't receive information");
    }
}
