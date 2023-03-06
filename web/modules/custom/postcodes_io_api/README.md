# Postcodes IO API
This module integrates Postcodes IO API with Drupal. 
This module provides a number of services that provide
access to various API endpoints and data.

## About Postcodes.io
Postcodes.io is a free Postcode & Geolocation API for the UK.
Free, Open Source and based solely on Open Data, 
Postcodes.io is aimed at developers who want a resource to 
lookup and (reverse) geocode UK Postcodes.

## Features
- Postcode lookup, resolve administrative and location data for 
postcodes and outward codes
- Postcode search & autocomplete
- Reverse geocode postcodes
- Nearest postcode search
- Terminated postcode search
- Outward code lookup
- Bulk postcode lookup and reverse geocoding

## Install Module
```
composer require drupal/postcodes_io_api
drush en postcodes_io_api
```

## Services:
### Postcodes IO API Client Service
Postcodes IO API Client, this is the core connector between Drupal and 
Postcodes IO. It uses Guzzle to request API calls.

### Methods:
#### Lookup a postcode
This uniquely identifies a postcode.

Example:
```
$params = ['postcode' => 'CF10 1DD'];
$result = $postcodesIoApiClient->lookup($params, TRUE);
```

#### Bulk Postcode Lookup.
Returns a list of matching postcodes and respective available data.

Example:
```
$result = $postcodesIoApiClient->bulkLookup($params, TRUE);
```

#### Reverse Geocoding.
Returns nearest postcodes for a given longitude and latitude.

Example:
```
$result = $postcodesIoApiClient->reverseGeocode($params, TRUE);
```

#### Bulk Reverse Geocoding
Bulk translates geolocations into Postcodes. 
Accepts up to 100 geolocations.

Example:
```
$result = $postcodesIoApiClient->bulkReverseGeocode($params, TRUE);
```

#### Postcode Query
Submit a postcode query and receive a complete list of postcode
matches and all associated postcode data.

Example:
```
$result = $postcodesIoApiClient->matching($params, TRUE);
```

#### Postcode Validation
Method to validate a postcode.

Example:
```
$result = $postcodesIoApiClient->validate($params, TRUE);
```

#### Postcode Autocomplete
Convenience method to return an list of matching postcodes.

Example:
```
$result = $postcodesIoApiClient->autocomplete($params, TRUE);
```

#### Random Postcode
Returns a random postcode and all available data for that postcode.

Example:
```
$result = $postcodesIoApiClient->random();
```

#### Outward Code Lookup
Geolocation data for the centroid of the outward code specified.

Example:
```
$result = $postcodesIoApiClient->outwardCodeLookup($params, TRUE);
```

## Resources
- [Public API](https://postcodes.io)
- [API Documentation](https://postcodes.io/docs)
- [3rd Party API Clients](https://postcodes.io/about)
- [Public API Service Status](https://status.ideal-postcodes.co.uk)
- [Self Hosting](https://postcodes.io/docs#Install-notes)
- [Explore](https://postcodes.io/explore)

## Running tests in Docker
```
sudo -u www-data php ./core/scripts/run-tests.sh \
  --sqlite /tmp/drupal/test.sqlite \
  --url http://localhost \
  --module postcodes_io_api \
  --verbose \
  --color
```
