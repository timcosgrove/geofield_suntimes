<?php

namespace Drupal\geofield_suntimes;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Http\ClientFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TimezoneService.
 */
class TimezoneService implements GeofieldSuntimesTimezoneInterface {

  /**
   * The factory for HTTP clients.
   *
   * @var \Drupal\Core\Http\ClientFactory
   */
  protected $httpClientFactory;

  /**
   * Constructs a new TimezoneService object.
   */
  public function __construct(ClientFactory $httpClientFactory) {
    $this->httpClientFactory = $httpClientFactory;
  }

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client_factory')
    );
  }

  /**
   * Creates an HTTP client.
   *
   * @return \GuzzleHttp\Client
   *   The HTTP Client.
   */
  public function getClient() {
    return $this->httpClientFactory->fromOptions();
  }

  /**
   * Retrieves timezone information given lat and lon.
   *
   * @param float $lat
   *   Latitude.
   * @param float $lng
   *   Longitude.
   *
   * @return array
   *   Information about the timezone.
   */
  public function getTimezone($lat, $lng) {
    $httpClient = $this->getClient();
    $url = 'https://maps.googleapis.com/maps/api/timezone/json';
    $location = $lat . ',' . $lng;
    $queryOptions = [
      'query' => [
        'location' => $location,
        // Key needs to be pulled from config or secrets.
        'key' => '',
        'timestamp' => time(),
      ],

    ];
    $response = $httpClient->request('GET', $url, $queryOptions);
    return Json::decode($response->getBody());

  }

}
