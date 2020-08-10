<?php

namespace Drupal\geofield_suntimes;

/**
 * Interface GeofieldSuntimesInterface.
 */
interface GeofieldSuntimesInterface {

  /**
   * Get requested "suntimes" from the API endpoint.
   *
   * @param float $lat
   *   Latitude.
   * @param float $lng
   *   Longitude.
   *
   * @return array
   *   An array of sunrise, sunset, and solar noon times.
   */
  public function getTimes($lat, $lng);

}
