<?php

namespace Nfq\WeatherBundle;

class DelegatingWeatherProvider implements WeatherProviderInterface
{
    private $providers;

    /**
     * DelegatingWeatherProvider constructor.
     * @param $providers
     */
    public function __construct($providers)
    {
        $this->providers = $providers;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(Location $location): Weather
    {
        foreach ($this->providers as $provider) {
            if ($provider) {
                return $provider;
            }
        }
        throw new WeatherProviderException("Weather providers can't receive information");
    }

}
