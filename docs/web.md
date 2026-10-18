# Web domains

```php
$web = $client->getModuleWeb('admin');
```

The web module works on the domains of one user, given when you get the module.

## Domains

```php
$web->listDomains(); // ['example.com' => ['IP' => '...', 'ALIAS' => '...', ...], ...]
$web->listDomain('example.com'); // the fields of one domain

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

## Aliases

```php
$web->addDomainAlias('example.com', 'www.example.com,shop.example.com'); // comma-separated

$web->deleteDomainAlias('example.com', 'shop.example.com'); // one at a time
```

## SSL certificates

```php
$web->listDomainSsl('example.com'); // ['CRT' => '...', 'KEY' => '...', 'CA' => '...', 'SUBJECT' => '...', 'NOT_AFTER' => '...', ...]
```

To use your own certificate, put `example.com.crt`, `example.com.key` and, if you have one, `example.com.ca` in a directory **on the HestiaCP server**, and pass that directory. HestiaCP copies the files, so the directory can be deleted afterwards. The API can't upload the files for you.

```php
use neto737\HestiaCP\Command\Web\AddWebDomainSsl;

$web->addDomainSsl('example.com', '/home/admin/tmp/ssl');

// serve HTTPS from public_shtml instead of public_html
$web->addDomainSsl('example.com', '/home/admin/tmp/ssl', AddWebDomainSsl::SSL_HOME_SINGLE);

$web->changeDomainSslCert('example.com', '/home/admin/tmp/ssl-renewed'); // replace the certificate
$web->changeDomainSslHome('example.com', AddWebDomainSsl::SSL_HOME_SAME);

$web->deleteDomainSsl('example.com');
```

The SSL home is `same` (HTTPS serves `public_html`, the default) or `single` (HTTPS serves `public_shtml`). Any other value throws an `InvalidArgumentException`.

Redirecting HTTP to HTTPS and HSTS need SSL to be on:

```php
$web->addDomainSslForce('example.com');
$web->deleteDomainSslForce('example.com');

$web->addDomainSslHsts('example.com');
$web->deleteDomainSslHsts('example.com');
```

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

## Templates

The template lists are server-wide, so they're the same whichever user the module is for.

```php
$web->listTemplates();        // ['default', 'hosting', ...]
$web->listBackendTemplates(); // ['default', 'PHP-8_3', ...]
$web->listProxyTemplates();   // ['caching', 'default', ...]

$web->changeDomainTemplate('example.com', 'hosting');
$web->changeDomainBackendTemplate('example.com', 'PHP-8_3');
$web->changeDomainProxyTemplate('example.com', 'caching');
$web->changeDomainProxyTemplate('example.com', 'caching', 'jpg,png,css,js'); // and the file extensions served by the proxy
```

Without extensions, `changeDomainProxyTemplate()` resets them to HestiaCP's default list.

## Proxy

```php
$web->addDomainProxy('example.com');                             // the user's default proxy template
$web->addDomainProxy('example.com', 'caching', 'jpg,png,css,js');

$web->deleteDomainProxy('example.com');
```

## FTP accounts

```php
$web->addDomainFtp('example.com', 'test', 'pa$$word');
$web->addDomainFtp('example.com', 'test', 'pa$$word', 'path/inside/domain'); // with a path

$web->changeDomainFtpPassword('example.com', 'admin_test', 'otherPa$$word');
$web->changeDomainFtpPath('example.com', 'admin_test', 'path/other');

$web->deleteDomainFtp('example.com', 'admin_test');
```

HestiaCP prefixes the FTP username with the owner's username. `addDomainFtp()` takes the name without the prefix (`test`); the other methods take the full name (`admin_test`).

## Restarting the web server

The alias, SSL, template and proxy methods take an optional last argument, `$restart`:

- `null` (default) leaves it to HestiaCP, which restarts the affected services right away, or queues the restart when the server is set up for scheduled restarts.
- `true` restarts them right away.
- `false` doesn't restart them.

```php
$web->addDomainAlias('example.com', 'www.example.com', false);
```

For `addDomainProxy()` and `changeDomainProxyTemplate()`, pass the extensions when you pass `$restart`. HestiaCP reads an empty extensions argument as no extensions, not as its default list, so leaving them `null` with `$restart` set throws an `InvalidArgumentException`. Pass `''` if you do want no extensions.

All methods except the `list*()` methods return `true` on success and throw an exception on failure (see [Error handling](errors.md)).
