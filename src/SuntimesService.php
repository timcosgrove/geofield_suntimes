<?php

namespace Drupal\geofield_suntimes;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Http\ClientFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SuntimesService.
 */
class SuntimesService implements GeofieldSuntimesInterface {

  /**
   * The factory for HTTP clients.
   *
   * @var \Drupal\Core\Http\ClientFactory
   */
  protected $httpClientFactory;

  /**
   * The timezone service.
   *
   * @var \Drupal\geofield_suntimes\TimezoneService
   */
  protected $timezoneService;

  /**
   * Constructs a new SuntimesService object.
   */
  public function __construct(ClientFactory $httpClientFactory, TimezoneService $timezoneService) {
    $this->httpClientFactory = $httpClientFactory;
    $this->timezoneService = $timezoneService;
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
      $container->get('http_client_factory'),
      $container->get('geofield_suntimes.timezone'),
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
  public function getTimes($lat, $lng) {
    $return_array = [
      'sunrise',
      'sunset',
      'solar_noon',
    ];
    $httpClient = $this->getClient();
    $url = 'https://api.sunrise-sunset.org/json';
    $queryOptions = [
      'query' => [
        'lat' => $lat,
        'lng' => $lng,
        'formatted' => '0',
      ],
    ];
    $defaultOptions = [
      'query' => [
        'lat' => '36.7201600',
        'lng' => '-4.4203400',
        'formatted' => 0,
      ],
    ];
    $queryOptions = array_merge($defaultOptions, $queryOptions);
    $response = $httpClient->request('GET', $url, $queryOptions);
    $body = Json::decode($response->getbody());
    $timezone = $this->timezoneService->getTimezone($lat, $lng);
    $output = [];
    foreach ($return_array as $event) {
      $output[$event] = DrupalDateTime::createFromFormat('Y-m-d\TH:i:sP', $body['results'][$event])->format('Y-m-d H:i:s T', ['timezone' => $timezone['timeZoneId']]);
    }
    return $output;

  }

}
