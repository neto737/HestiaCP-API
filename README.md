# HestiaCP PHP API

[![Name](https://badgen.net/packagist/name/neto737/hestiacp-api)](//packagist.org/packages/neto737/hestiacp-api) [![Latest Stable Version](https://badgen.net/packagist/v/neto737/hestiacp-api/latest)](https://packagist.org/packages/neto737/hestiacp-api) [![Total Downloads](https://badgen.net/packagist/dt/neto737/hestiacp-api)](https://packagist.org/packages/neto737/hestiacp-api)  [![License](https://badgen.net/packagist/license/neto737/hestiacp-api)](https://packagist.org/packages/neto737/hestiacp-api) [![Tests](https://github.com/neto737/HestiaCP-API/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/neto737/HestiaCP-API/actions/workflows/tests.yml) [![codecov](https://codecov.io/gh/neto737/HestiaCP-API/branch/main/graph/badge.svg?token=F93I5JQXY6)](https://codecov.io/gh/neto737/HestiaCP-API)

A PHP client for the [HestiaCP](https://hestiacp.com) API. It manages users, web domains, mail, databases and backups on a HestiaCP server.

## Requirements

- PHP 8.1 or later
- HestiaCP with the API enabled, and the IP address of the calling machine allowed in the server settings

## Installation

```sh
composer require neto737/hestiacp-api
```

## Quick start

```php
use neto737\HestiaCP\Client;

// Access key (HestiaCP 1.6+), written as ACCESS_KEY:SECRET_KEY
$client = Client::simpleFactory('https://panel.example.com', 'ACCESS_KEY:SECRET_KEY');

// Check that the credentials work
$client->testAuthorization(); // bool

// List all users
$users = $client->getModuleUser()->list(); // ['admin' => ['NAME' => '...', 'CONTACT' => '...', ...], ...]

// Add a web domain for the admin user
$client->getModuleWeb('admin')->addDomain('example.com');
```

TLS certificates are verified by default. If your panel still uses the self-signed certificate created at install, see [TLS certificates](docs/authentication.md#tls-certificates).

Failures are thrown as exceptions (`AuthorizationException`, `ProcessException`, `ClientException`). See [Error handling](docs/errors.md).

## Documentation

- [Authentication and client setup](docs/authentication.md): access keys, legacy API keys, passwords, hostname rules, TLS and timeouts
- [Error handling](docs/errors.md): exception types and HestiaCP exit codes
- Modules:
  - [Users](docs/users.md)
  - [Web domains](docs/web.md)
  - [Mail](docs/mail.md)
  - [Databases](docs/databases.md)
  - [Backups](docs/backups.md)
- [Sending commands directly](docs/commands.md): using the command classes, and writing your own

Upgrading from v1? Read [UPGRADING.md](UPGRADING.md). All changes are listed in [CHANGELOG.md](CHANGELOG.md).

## Donate :heart:

```
BTC: bc1q89ntljt5lk7g9z68f5cjs83qfm2xme7g4hkur7
ETH: 0xeef9220639F14E7A0FD825AAAd0574e5a8aD7A4B
LTC: ltc1q508qfkd09vyya6c5zkfx4r248pf3ezj9ngjdr2
```
