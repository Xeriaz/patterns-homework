<?php

namespace Nfq\WeatherBundle;

class YahooWeatherProvider implements WeatherProviderInterface
{
    private $BASE_URL = "http://query.yahooapis.com/v1/public/yql";

    /**
     * {@inheritdoc}
     */
    public function fetch(Location $location): Weather
    {
        $yql_query = $this->getQuery($location);
        $yql_query_url = $this->BASE_URL . "?q=" . urlencode($yql_query) . "&format=json";

        // Make call with cURL
        $session = curl_init($yql_query_url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER,true);

        $json = curl_exec($session);
        // Convert JSON to PHP object
        $phpObj = json_decode($json);

        if(@$phpObj->error) {
            throw new \InvalidArgumentException('Error: ' . $phpObj->error->description);
        }

        $temp = $phpObj->query->results->channel->item->condition->temp;

        if (!$temp) {
            throw new \InvalidArgumentException('Provider error');
        }

        return new Weather($temp);
    }

    private function getQuery(Location $location): string
    {
        $lat = $location->getLat();
        $lon = $location->getLon();

        $query = 'SELECT item.condition.temp FROM weather.forecast '.
            ' WHERE woeid IN (SELECT woeid FROM geo.places WHERE text="(' .
            $lat . ',' . $lon .')") and u="c"';

        return $query;
    }
}
