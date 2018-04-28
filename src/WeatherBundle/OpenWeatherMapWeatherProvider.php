<?php

namespace Nfq\WeatherBundle;

class OpenWeatherMapWeatherProvider implements WeatherProviderInterface
{
    private $BASE_URL = 'http://api.openweathermap.org/data/2.5/weather?id=524901&APPID=';
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(Location $location): Weather
    {
        $url = $this->BASE_URL . $this->apiKey .
            $this->getWeatherByCoordQuery($location);

        if (!@file_get_contents($url)) {
            throw new \InvalidArgumentException(sprintf('Given url path "%s" does not exist', $url));
        }

        $json = file_get_contents($url);
        $phpObj = json_decode($json);
        $temp = floatval($phpObj->main->temp);

        return new Weather($temp);
    }

    private function getWeatherByCoordQuery(Location $location): string
    {
        $lat = $location->getLat();
        $lon = $location->getLon();
        return '&weather&lat='. $lat .'&lon=' . $lon . '&units=metric';
    }
}
