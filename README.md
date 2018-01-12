# WooCommerce API - PHP Client

A PHP wrapper for the WooCommerce REST API. Easily interact with the WooCommerce REST API securely using this library. If using a HTTPS connection this library uses BasicAuth, else it uses Oauth to provide a secure connection to WooCommerce.

[![build status](https://secure.travis-ci.org/woocommerce/wc-api-php.svg)](http://travis-ci.org/woocommerce/wc-api-php)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/woocommerce/wc-api-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/woocommerce/wc-api-php/?branch=master)
[![PHP version](https://badge.fury.io/ph/automattic%2Fwoocommerce.svg)](https://packagist.org/packages/automattic/woocommerce)

## Installation

```
composer require automattic/woocommerce
```

## Getting started

Generate API credentials (Consumer Key & Consumer Secret) following this instructions <http://docs.woocommerce.com/document/woocommerce-rest-api/>
.

Check out the WooCommerce API endpoints and data that can be manipulated in <https://woocommerce.github.io/woocommerce-rest-api-docs/>.

## Setup

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
| `wp_api_prefix`     | `string` | no       | Custom WP REST API URL prefix, used to support custom prefixes created with the `rest_url_prefix` filter               |
| `version`           | `string` | no       | API version, default is `v3`                                                                                           |
| `timeout`           | `int`    | no       | Request timeout, default is `15`                                                                                       |
| `follow_redirects`  | `bool`   | no       | Allow the API call to follow redirects                                                                                 |
| `verify_ssl`        | `bool`   | no       | Verify SSL when connect, use this option as `false` when need to test with self-signed certificates, default is `true` |
| `query_string_auth` | `bool`   | no       | Force Basic Authentication as query string when `true` and using under HTTPS, default is `false`                       |
| `oauth_timestamp`   | `string` | no       | Custom oAuth timestamp, default is `time()`                                                                            |
| `user_agent`        | `string` | no       | Custom user-agent, default is `WooCommerce API Client-PHP`                                                             |

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
    $lastRequest->getUrl(); // Requested URL (string).
    $lastRequest->getMethod(); // Request method (string).
    $lastRequest->getParameters(); // Request parameters (array).
    $lastRequest->getHeaders(); // Request headers (array).
    $lastRequest->getBody(); // Request body (JSON).

    // Last response data.
    $lastResponse = $woocommerce->http->getResponse();
    $lastResponse->getCode(); // Response code (int).
    $lastResponse->getHeaders(); // Response headers (array).
    $lastResponse->getBody(); // Response body (JSON).

} catch (HttpClientException $e) {
    $e->getMessage(); // Error message.
    $e->getRequest(); // Last request data.
    $e->getResponse(); // Last response data.
}
```

## Release History

- 2018-01-12 - 2.0.0 - Responses changes from arrays to `stdClass` objects. Added `follow_redirects` option.
- 2017-06-06 - 1.3.0 - Remove BOM before decoding and added support for multi-dimensional arrays for oAuth1.0a.
- 2017-03-15 - 1.2.0 - Added `user_agent` option.
- 2016-12-14 - 1.1.4 - Fixed WordPress 4.7 compatibility.
- 2016-10-26 - 1.1.3 - Allow set `oauth_timestamp` and improved how is handled the response headers.
- 2016-09-30 - 1.1.2 - Added `wp_api_prefix` option to allow custom WP REST API URL prefix.
- 2016-05-10 - 1.1.1 - Fixed oAuth and error handler for WP REST API.
- 2016-05-09 - 1.1.0 - Added support for WP REST API, added method `Automattic\WooCommerce\Client::options` and fixed multiple headers responses.
- 2016-01-25 - 1.0.2 - Fixed an error when getting data containing non-latin characters.
- 2016-01-21 - 1.0.1 - Sort all oAuth parameters before build request URLs.
- 2016-01-11 - 1.0.0 - Stable release.
