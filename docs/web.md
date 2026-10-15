# Web domains

```php
$web = $client->getModuleWeb('admin');
```

The web module works on the domains of one user, given when you get the module.

## Domains

```php
$web->listDomains(); // ['example.com' => ['IP' => '...', 'ALIAS' => '...', ...], ...]

$web->addDomain('example.com');

$web->suspendDomain('example.com');
$web->unsuspendDomain('example.com');

$web->deleteDomain('example.com');
```

`addDomain()` also takes these optional arguments:

```php
$web->addDomain(
	'example.com',
	'192.0.2.10',       // IP address (default: chosen by HestiaCP)
	'www.example.com',  // aliases, comma-separated
	'jpg,png,css,js',   // file extensions served by the proxy
	true                // restart the web server
);
```

`suspendDomain()` and `unsuspendDomain()` take an optional `$restart` argument (default `false`).

## Let's Encrypt

```php
$web->addDomainLetsEncrypt('example.com', 'www.example.com');

// certificate for the mail domain as well
$web->addDomainLetsEncrypt('example.com', null, true);

// key algorithm: ecdsa-256 (default), ecdsa-384 or rsa
$web->addDomainLetsEncrypt('example.com', 'www.example.com', false, 'rsa');

$web->deleteDomainLetsEncrypt('example.com');
```

Getting a certificate is slow, so this command uses a timeout of at least 90 seconds.

The key algorithm argument needs a HestiaCP version newer than 1.10.5. Leave it `null` on older servers, and it won't be sent.

## FTP accounts

```php
$web->addDomainFtp('example.com', 'test', 'pa$$word');
$web->addDomainFtp('example.com', 'test', 'pa$$word', 'path/inside/domain'); // with a path

$web->changeDomainFtpPassword('example.com', 'admin_test', 'otherPa$$word');
$web->changeDomainFtpPath('example.com', 'admin_test', 'path/other');

$web->deleteDomainFtp('example.com', 'admin_test');
```

HestiaCP prefixes the FTP username with the owner's username. `addDomainFtp()` takes the name without the prefix (`test`); the other methods take the full name (`admin_test`).

All methods except `listDomains()` return `true` on success and throw an exception on failure (see [Error handling](errors.md)).
