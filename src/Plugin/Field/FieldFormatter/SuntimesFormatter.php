<?php

namespace Drupal\geofield_suntimes\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\geofield\GeoPHP\GeoPHPInterface;
use Drupal\geofield\Plugin\Field\FieldFormatter\LatLonFormatter;
use Drupal\geofield_suntimes\GeofieldSuntimesInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'geofield_latlon' formatter.
 *
 * @FieldFormatter(
 *   id = "latlon_suntimes",
 *   label = @Translation("Lat/Lon & Suntimes"),
 *   field_types = {
 *     "geofield"
 *   }
 * )
 */
class SunTimesFormatter extends LatLonFormatter implements ContainerFactoryPluginInterface {

  /**
   * The suntimes retrieval service.
   *
   * @var \Drupal\geofield_suntimes\GeofieldSuntimesInterface
   */
  protected $suntimesService;

  /**
   * Construct a SuntimesFormatter object.
   *
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   Defines an interface for entity field definitions.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\geofield\GeoPHP\GeoPHPInterface $geophp_wrapper
   *   The geoPhpWrapper.
   * @param \Drupal\geofield_suntimes\GeofieldSuntimesInterface $suntimes_service
   *   The suntimes service.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    GeoPHPInterface $geophp_wrapper,
    GeofieldSuntimesInterface $suntimes_service
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings, $geophp_wrapper);
    $this->suntimesService = $suntimes_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('geofield.geophp'),
      $container->get('geofield_suntimes.default'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    // Let the parent format and theme the data.
    $latlon_elements = parent::viewElements($items, $langcode);

    foreach ($items as $delta => $item) {
      $output = ['#markup' => ''];
      $geom = $this->geoPhpWrapper->load($item->value);
      if ($geom && $geom->getGeomType() == 'Point') {
        /* @var \Point $geom */
        // Retrieve the suntimes using lat and lon.
        $suntimes = $this->suntimesService->getTimes($geom->y(), $geom->x());
        $output = [
          '#theme' => 'geofield_suntimes',
          '#sunrise' => $suntimes['sunrise'],
          '#sunset' => $suntimes['sunset'],
          '#solar_noon' => $suntimes['solar_noon'],
        ];
      }
      // Append this output after the parent output.
      $elements[$delta] = [
        $latlon_elements[$delta],
        $output,
      ];
    }

    return $elements;

  }

}
