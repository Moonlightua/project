<?php

namespace Drupal\currency\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Currency' Block.
 *
 * @Block(
 *   id = "currency",
 *   admin_label = @Translation("Currency rates"),
 *   category = @Translation("Currency"),
 * )
 */
class Currency extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $date = $this->getCurrency();

    $build['content'] = [
        '#theme' => 'currency',
        '#currencies' => $this->getCurrency(),
    ];

    return $build;

  }

  protected function getCurrency () {

    // Fetching JSON
    $req_url = 'https://v6.exchangerate-api.com/v6/25bf1245024cb737116446f4/latest/UAH';
    $response_json = file_get_contents($req_url);

    // Continuing if we got a result
    if(false !== $response_json) {

      // Decoding
      $response = json_decode($response_json);

      // Check for success
      if ('success' === $response->result) {

        $currency = (array)$response->conversion_rates;

        $last_update_date = $response->time_last_update_utc;
        $next_update_date = $response->time_next_update_utc;

        $data = [];
        $data[2] = substr($last_update_date, 0, -6);
        $data[1] = substr($next_update_date,0, -6);

        foreach ($currency as $key => $value) {
          if ($key == 'USD' or $key == 'EUR' or $key == 'GBP' or $key == 'PLN'
            or $key == 'CAD' || $key == 'CHF' || $key == 'AUD' || $key == 'HUF'
            || $key == 'CZK' || $key == 'JPY' || $key == 'CNY') {
            $data[$key] = round(1/$value,2);
          }
        }
      }
    }

    return $data;
  }

}
