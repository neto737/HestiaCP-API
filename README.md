
# HestiaCP PHP API

  
[![Name](https://badgen.net/packagist/name/neto737/hestiacp-api)](//packagist.org/packages/neto737/hestiacp-api) [![Latest Stable Version](https://badgen.net/packagist/v/neto737/hestiacp-api/latest)](https://packagist.org/packages/neto737/hestiacp-api) [![Total Downloads](https://badgen.net/packagist/dt/neto737/hestiacp-api)](https://packagist.org/packages/neto737/hestiacp-api)  [![License](https://badgen.net/packagist/license/neto737/hestiacp-api)](https://packagist.org/packages/neto737/hestiacp-api) [![Build Status](https://www.travis-ci.com/neto737/HestiaCP-API.svg?branch=main)](https://www.travis-ci.com/github/neto737/HestiaCP-API) [![codecov](https://codecov.io/gh/neto737/HestiaCP-API/branch/main/graph/badge.svg?token=F93I5JQXY6)](https://codecov.io/gh/neto737/HestiaCP-API)

  

## How to use

1) Installation

```sh

$ composer require neto737/hestiacp-api

```

2) Create Client

a) Easy way

```php

use neto737\HestiaCP\Client;

// Easy way to create Client

// Using an access key (HestiaCP 1.6+), written as ACCESS_KEY:SECRET_KEY
$client = Client::simpleFactory('https://someHost', 'ACCESS_KEY:SECRET_KEY');

// Using a legacy API key
$client = Client::simpleFactory('https://someHost', 'API_Key');

// Using username and password (HestiaCP only accepts the admin/root user)
$client = Client::simpleFactory('https://someHost', 'admin', 'somePass');

```

The hostname must start with `https://` (or `http://`) and contain only the host, for example `https://panel.example.com`. An IP address works too: `https://192.0.2.10`, or `https://[2001:db8::1]` for IPv6 (the brackets are required). A trailing slash is removed. The port is a separate argument of `Host` (default `8083`). An invalid hostname throws `\neto737\HestiaCP\Exceptions\InvalidArgumentException`.

The API has to be enabled in the HestiaCP server settings, and the IP address of the machine that calls it has to be allowed.

Client options can be passed as the last argument of `simpleFactory()` or `new Client()`:

```php

$client = Client::simpleFactory('https://someHost', 'ACCESS_KEY:SECRET_KEY', null, [
	'timeout' => 30,     // seconds, default 10
	'verify' => false,   // default true; set to false for a self-signed certificate, or give a path to a CA bundle
]);

```

> TLS certificates are verified by default. Older versions of this library never checked them. If your panel uses the self-signed certificate created at install, either install a valid certificate (for example with Let's Encrypt) or pass `'verify' => false`.

b) For some reasons (more hosts, etc) you may need create objects alone

```php

use neto737\HestiaCP\Client;
use neto737\HestiaCP\Authorization\Credentials;
use neto737\HestiaCP\Authorization\Host;

// You can choose to use an access key, a legacy API key, or username and password

// Access key (HestiaCP 1.6+)
$credentials = Credentials::accessKey('ACCESS_KEY', 'SECRET_KEY');

// Legacy API Key
$credentials = Credentials::legacyHash('API_Key');

// Username and Password (HestiaCP only accepts the admin/root user)
$credentials = Credentials::password('admin', 'somePassword');

$host = new Host('https://someHost', $credentials);

$client = new Client($host);

```

3) Usage

```php

// verify login (runs v-list-sys-info, so the credentials must be allowed to run it)
$client->testAuthorization(); // bool

```

Errors are thrown as exceptions. The HestiaCP exit code is available as the exception code (see `\neto737\HestiaCP\Command\ResponseCode`):

```php

use neto737\HestiaCP\Exceptions\AuthorizationException;
use neto737\HestiaCP\Exceptions\ClientException;
use neto737\HestiaCP\Exceptions\ProcessException;

try {
	$client->getModuleUser()->add('other_user', 'pa$$word', 'some@email.com');
} catch (AuthorizationException $e) {
	// bad credentials, or not allowed to run the command (exit code 9 or 10)
} catch (ProcessException $e) {
	// the command failed, e.g. $e->getCode() === ResponseCode::E_EXISTS
} catch (ClientException $e) {
	// connection problem (timeout, DNS, TLS, ...)
}

```

You can simply send one of prepared commands (or you can write own command - must implements `\neto737\HestiaCP\Command\ICommand` )

```php

$command = new SomeCommand();

$response = $client->send($command);


echo $response->getResponseText();

```

Or you can use prepared modules

List and detail methods return plain associative arrays decoded from HestiaCP's JSON, keyed by the object name:

```php

$users = $client->getModuleUser()->list(); // ['admin' => ['NAME' => '...', 'CONTACT' => '...', ...], ...]

$admin = $client->getModuleUser()->detail('admin');
echo $admin['CONTACT'];

```

> Before v2.0 these methods returned `Nette\Utils\ArrayHash` objects. To upgrade, replace `$item->FIELD` with `$item['FIELD']`.

a) user module

```php

$userModule = $client->getModuleUser();


$userModule->list(); // returns all users with data

$userModule->detail('admin'); // returns selected user with data

$userModule->changePassword('admin', 'otherPa$$word');

$userModule->add('other_user', 'pa$$word', 'some@email.com');

$userModule->suspend('other_user');

$userModule->unsuspend('other_user');

$userModule->delete('other_user');

```

b) web module

```php

$webModule = $client->getModuleWeb('admin'); // web module needs user


$webModule->listDomains();

$webModule->addDomain('domain.com');

$webModule->addDomainLetsEncrypt('domain.com', 'www.domain.com'); // needs longer timeout

$webModule->deleteDomainLetsEncrypt('domain.com');

$webModule->addDomainFtp('domain.com', 'test', 'pa$$word');

$webModule->changeDomainFtpPassword('domain.com', 'admin_test', 'otherPa$$word');

$webModule->changeDomainFtpPath('domain.com', 'admin_test', 'path/other');

$webModule->deleteDomainFtp('domain.com', 'admin_test');

$webModule->suspendDomain('domain.com');

$webModule->unsuspendDomain('domain.com');

$webModule->deleteDomain('domain.com');

```

c) mail module

```php

$mailModule = $client->getModuleMail('admin'); // mail module needs user


$mailModule->listDomains(); // returns mail domains from selected user

$mailModule->addDomain('domain.com'); // add domain

$mailModule->listAccounts('domain.com'); // returns accounts from selected user and domain

$mailModule->listDomainDkim('domain.com');

$mailModule->listDomainDkimDns('domain.com');

$mailModule->addAccount('domain.com', 'info', 'pa$$word'); // add info@domain.com account

$mailModule->changeAccountPassword('domain.com', 'info', 'otherPa$$word'); // change info@domain.com password

$mailModule->suspendAccount('domain.com', 'info'); // suspend info@domain.com account

$mailModule->unsuspendAccount('domain.com', 'info'); // unsuspend info@domain.com account

$mailModule->deleteAccount('domain.com', 'info');

$mailModule->suspendDomain('domain.com');

$mailModule->unsuspendDomain('domain.com');

$mailModule->deleteDomain('domain.com');

```

d) db module

```php

$dbModule = $client->getModuleDatabase('admin');


$dbModule->add('database', 'dbuser', 'dbpass');

$dbModule->delete('admin_database');

$dbModule->deleteDatabases();

$dbModule->listDatabase('database');

$dbModule->listDatabases();


// todo

// ... etc

```

e) cron module

```php

$cronModule = $client->getModuleCron();


// todo

// ... etc

```

f) backup module

```php

$backupModule = $client->getModuleBackup('admin'); // backup module needs user

$backupModule->backup(); // create a new backup (waits until the backup is done, no timeout)

$backupModule->delete('admin.2021-10-13_18-12-53.tar'); // delete an user backup

$backupModule->deleteExclusions(); // delete all backup exclusions

$backupModule->listBackups(); // returns the backups list

$backupModule->listBackup('admin.2021-10-13_18-12-53.tar'); // returns backup parameters list

$backupModule->listBackupExclusions(); // returns the backup exclusions list

```

## Donate :heart:

```
BTC: bc1q89ntljt5lk7g9z68f5cjs83qfm2xme7g4hkur7
ETH: 0xeef9220639F14E7A0FD825AAAd0574e5a8aD7A4B
LTC: ltc1q508qfkd09vyya6c5zkfx4r248pf3ezj9ngjdr2
```
