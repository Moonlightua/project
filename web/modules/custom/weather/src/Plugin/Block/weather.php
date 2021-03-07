<?php

namespace Drupal\weather\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Weather' Block.
 *
 * @Block(
 *   id = "weather",
 *   admin_label = @Translation("Weather"),
 *   category = @Translation("Weather"),
 * )
 */
class Weather extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $date = date("F j l, G:i",strtotime('+2 hours'));

    $build['content'] = [
        '#theme' => 'weather',
        '#data' => $this->getWeather(),
        '#date' => $date,
    ];

    return $build;

  }

  protected function getWeather() {

    $apiKey = "2bfbaf50b2e9b96d175d14a762a40cf7";
    $cityId = "702569";
    $googleApiUrl = "http://api.openweathermap.org/data/2.5/weather?id=" . $cityId . "&lang=en&units=metric&APPID=" . $apiKey;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);

    curl_close($ch);

    $data = json_decode($response);

    return $data;
  }

}
