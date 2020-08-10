<?php

namespace Drupal\geofield_suntimes;

/**
 * Interface GeofieldSuntimesTimezoneInterface.
 */
interface GeofieldSuntimesTimezoneInterface {

  /**
   * Get the timezone from the API endpoint using lat and lon.
   *
   * @param float $lat
   *   Latitude.
   * @param float $lng
   *   Longitude.
   *
   * @return array
   *   An array timezone information.
   */
  public function getTimezone($lat, $lng);

}
