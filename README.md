# WooCommerce API - PHP Client

A PHP wrapper for the WooCommerce REST API. Easily interact with the WooCommerce REST API using this library.

[![build status](https://secure.travis-ci.org/woothemes/wc-api-php.svg)](http://travis-ci.org/woothemes/wc-api-php)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/woothemes/wc-api-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/woothemes/wc-api-php/?branch=master)
[![PHP version](https://badge.fury.io/ph/automattic%2Fwoocommerce.svg)](https://packagist.org/packages/automattic/woocommerce)

## Installation

```
composer require automattic/woocommerce
```

## Getting started

Generate API credentials (Consumer Key & Consumer Secret) following this instructions <http://docs.woothemes.com/document/woocommerce-rest-api/>
.

Check out the WooCommerce API endpoints and data that can be manipulated in <http://woothemes.github.io/woocommerce-rest-api-docs/>.

## Setup

Setup for the old WooCommerce API v3:

```php
require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;

$woocommerce = new Client(
    'http://example.com', 
    'ck_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', 
    'cs_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
    [
        'version' => 'v3',
    ]
);
```

Setup for the new WP REST API integration (WooCommerce 2.6 or later):

```php
require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;

$woocommerce = new Client(
    'http://example.com', 
    'ck_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', 
    'cs_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
    [
        'wp_api' => true,
        'version' => 'wc/v1',
    ]
);
```

### Options

|       Option      |   Type   | Required |                Description                 |
| ----------------- | -------- | -------- | ------------------------------------------ |
| `url`             | `string` | yes      | Your Store URL, example: http://woo.dev/   |
| `consumer_key`    | `string` | yes      | Your API consumer key                      |
| `consumer_secret` | `string` | yes      | Your API consumer secret                   |
| `options`         | `array`  | no       | Extra arguments (see client options table) |

#### Client options

|        Option       |   Type   | Required |                                                      Description                                                       |
|---------------------|----------|----------|------------------------------------------------------------------------------------------------------------------------|
| `wp_api`            | `bool`   | no       | Allow make requests to the new WP REST API integration (WooCommerce 2.6 or later)                                      |
| `version`           | `string` | no       | API version, default is `v3`                                                                                           |
| `timeout`           | `int`    | no       | Request timeout, default is `15`                                                                                       |
| `verify_ssl`        | `bool`   | no       | Verify SSL when connect, use this option as `false` when need to test with self-signed certificates, default is `true` |
| `query_string_auth` | `bool`   | no       | When `true` and using under HTTPS force Basic Authentication as query string, default is `false`                       |

## Methods

|    Params    |   Type   |                         Description                          |
| ------------ | -------- | ------------------------------------------------------------ |
| `endpoint`   | `string` | WooCommerce API endpoint, example: `customers` or `order/12` |
| `data`       | `array`  | Only for POST and PUT, data that will be converted to JSON   |
| `parameters` | `array`  | Only for GET and DELETE, request query string                |

### GET

```php
$woocommerce->get($endpoint, $parameters = [])
```

### POST

```php
$woocommerce->post($endpoint, $data)
```

### PUT

```php
$woocommerce->put($endpoint, $data)
```

### DELETE

```php
$woocommerce->delete($endpoint, $parameters = [])
```

### OPTIONS

```php
$woocommerce->options($endpoint)
```

#### Response

All methods will return arrays on success or throwing `HttpClientException` errors on failure.


```php
use Automattic\WooCommerce\HttpClient\HttpClientException;

try {
    // Array of response results.
    $results = $woocommerce->get('customers');
    // Example: ['customers' => [[ 'id' => 8, 'created_at' => '2015-05-06T17:43:51Z', 'email' => ...

    // Last request data.
    $lastRequest = $woocommerce->http->getRequest();
    $lastRequest->getUrl() // Requested URL (string).
    $lastRequest->getMethod() // Request method (string).
    $lastRequest->getParameters() // Request parameters (array).
    $lastRequest->getHeaders() // Request headers (array).
    $lastRequest->getBody() // Request body (JSON).

    // Last response data.
    $lastResponse = $woocommerce->http->getResponse();
    $lastResponse->getCode(); // Response code (int).
    $lastResponse->getHeaders(); // Response headers (array).
    $lastResponse->getBody(); // Response body (JSON).

} catch (HttpClientException $e) {
    $e->getMessage() // Error message.
    $e->getRequest() // Last request data.
    $e->getResponse() // Last response data.
}
```

## Release History

- 2016-05-10 - 1.1.1 - Fixed oAuth and error handler for WP REST API.
- 2016-05-09 - 1.1.0 - Added support for WP REST API, added method `Automattic\WooCommerce\Client::options` and fixed multiple headers responses.
- 2016-01-25 - 1.0.2 - Fixed an error when getting data containing non-latin characters.
- 2016-01-21 - 1.0.1 - Sort all oAuth parameters before build request URLs.
- 2016-01-11 - 1.0.0 - Stable release.
