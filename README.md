# Geofield Suntimes
Simple service that works with the API provided by https://sunrise-sunset.org/api for converting lat/lon to times for sunrise/sunset and others for a given date.

## Requirements
* Drupal 8.x
* https://www.drupal.org/project/geofield
* The demo requires https://www.drupal.org/project/geofield_map

## Installation
Given a Composer-managed Drupal 8.x installation:
```
composer config repositories.suntimes vcs https://github.com/timcosgrove/geofield_suntimes.git

composer require timcosgrove/geofield_suntimes:dev-master

# Note we're intalling the demo module which has the primary module as a dependency.
drush pm-enable geofield_suntimes_demo
```

Additionally, you need to provide a Google api key that has permissions set to use:
* Maps JavaScript API
* Places API
* Geocoding API
* Time Zone API

This key can then be made available in the `settings.php` or equivalent for your site as follows:

```

$config['geofield_suntimes.settings'] = [
  'gmap_api_key' => '<your key>'
];
$config['geofield_map.settings'] = [
  'gmap_api_key' => '<your key>'
];
```

## Demo
The demo module provides a content type 'Park' with a single editable field, which uses the geofield_map form widget for a geofield field such that the user can search for a location via Google Maps and save that location data.

The field's latitude and longitude values are passed to geofield_suntimes's services to return the time of sunrise, sunset, and solar noon for that location on the current date. The module provides a field display formatter to automatically display this data.
