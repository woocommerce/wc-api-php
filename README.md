# WooCommerce API - PHP Client

A PHP wrapper for the WooCommerce REST API. Easily interact with the WooCommerce REST API using this library.

## Installation

```
composer require automattic/woocommerce
```

## Getting started

Generate API credentials (Consumer Key & Consumer Secret) following this instructions <http://docs.woothemes.com/document/woocommerce-rest-api/>
.

Check out the WooCommerce API endpoints and data that can be manipulated in <http://woothemes.github.io/woocommerce-rest-api-docs/>.

## Setup

```php
require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;

$woocommerce = new Client(
    'http://example.com', 
    'ck_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', 
    'cs_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'
);
```

### Options

|       Option      |   Type   | Required |               Description                |
| ----------------- | -------- | -------- | ---------------------------------------- |
| `url`             | `string` | yes      | Your Store URL, example: http://woo.dev/ |
| `consumer_key`    | `string` | yes      | Your API consumer key                    |
| `consumer_secret` | `string` | yes      | Your API consumer secret                 |
| `options`         | `array`  | no       | Extra arguments (see client options table) |

#### Client options

|        Option       |   Type   | Required |                                             Description                                             |
| ------------------- | -------- | -------- | --------------------------------------------------------------------------------------------------- |
| `version`           | `string` | no       | API version, default is `v3`                                                                        |
| `timeout`           | `int`    | no       | Request timeout                                                                                     |
| `verify_ssl`        | `bool`   | no       | Verify SSL when connect, use this option as `false` when need to test with self-signed certificates |
| `query_string_auth` | `bool`   | no       | When `true` and using under HTTPS force Basic Authentication as query string                        |

## Methods

|    Params    |   Type   |                         Description                          |
| ------------ | -------- | ------------------------------------------------------------ |
| `endpoint`   | `string` | WooCommerce API endpoint, example: `customers` or `order/12` |
| `data`       | `array`  | Only for POST and PUT, data that will be converted to JSON   |
| `parameters` | `array`  | Only for GET and DELETE, request query string                |

### GET

- `$woocommerce->get($endpoint, $parameters = [])`

### POST

- `$woocommerce->post($endpoint, $data)`

### PUT

- `$woocommerce->put($endpoint, $data)`

### DELETE

- `$woocommerce->delete($endpoint, $parameters = [])`
