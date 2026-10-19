# Authentication and client setup

## Before you start

The API has to be enabled in the HestiaCP server settings, and the IP address of the machine that calls it has to be allowed. Otherwise every request fails.

## Credentials

HestiaCP accepts three kinds of credentials.

| Kind | HestiaCP version | Notes |
| --- | --- | --- |
| Access key + secret key | 1.6+ | Recommended. Create them in the panel under the user's access keys. |
| Legacy API key | all | The old single `hash` key. |
| Username + password | all | Only accepted for the root/admin user. |

## Creating a client

### The short way

`Client::simpleFactory()` takes the hostname and the credentials as strings:

```php
use neto737\HestiaCP\Client;

// Access key, written as ACCESS_KEY:SECRET_KEY
$client = Client::simpleFactory('https://panel.example.com', 'ACCESS_KEY:SECRET_KEY');

// Legacy API key
$client = Client::simpleFactory('https://panel.example.com', 'API_KEY');

// Username and password (admin/root user only)
$client = Client::simpleFactory('https://panel.example.com', 'admin', 'somePassword');
```

### Building the objects yourself

Use this when you need a different port, or want to reuse the same credentials for several hosts:

```php
use neto737\HestiaCP\Client;
use neto737\HestiaCP\Authorization\Credentials;
use neto737\HestiaCP\Authorization\Host;

$credentials = Credentials::accessKey('ACCESS_KEY', 'SECRET_KEY');
// or Credentials::legacyHash('API_KEY');
// or Credentials::password('admin', 'somePassword');

$host = new Host('https://panel.example.com', $credentials); // port defaults to 8083
$host = new Host('https://panel.example.com', $credentials, 443); // a different port

$client = new Client($host);
```

`Credentials` can only be created with the named constructors above. `Credentials`, `Host` and the client's host can't be changed afterwards: to connect to another server, or with other credentials, create a new `Host` and `Client`.

## Hostname

The hostname must start with `https://` (or `http://`) and contain only the host:

- `https://panel.example.com`
- `https://192.0.2.10`
- `https://[2001:db8::1]` (IPv6 addresses need the brackets)

A trailing slash is removed, and the scheme and host are lowercased. The port is a separate argument of `Host`, not part of the hostname. A hostname with a port, path, query, fragment or user info, or without a scheme, throws `\neto737\HestiaCP\Exceptions\InvalidArgumentException`.

## Client options

Options are passed as the last argument of `Client::simpleFactory()` or `new Client()`:

```php
$client = Client::simpleFactory('https://panel.example.com', 'ACCESS_KEY:SECRET_KEY', null, [
	'timeout' => 30,
	'verify' => '/path/to/ca-bundle.pem',
]);

$client = new Client($host, ['timeout' => 30]);
```

| Option | Default | Description |
| --- | --- | --- |
| `timeout` | `10` | Request timeout in seconds. `0` means no timeout. |
| `verify` | `true` | Verify the TLS certificate. `false` turns verification off; a string is the path to a CA bundle. |
| `handler` | Guzzle default | A custom Guzzle handler stack, for middleware or tests. |

Some commands need more time and raise the timeout themselves: adding a Let's Encrypt certificate waits at least 90 seconds, and backups have no timeout. A command never uses a shorter timeout than the client's.

## TLS certificates

TLS certificates are verified by default. If your panel still uses the self-signed certificate that HestiaCP creates at install, either:

- install a valid certificate (for example with Let's Encrypt), or
- pass the path to a CA bundle that contains your certificate as `verify`, or
- pass `'verify' => false` to turn verification off. Only do this on a network you trust, because it makes the connection open to interception.

## Checking the credentials

```php
$client->testAuthorization(); // true, or false on an authentication failure
```

It runs `v-list-sys-info`, so the credentials must be allowed to run that command.
