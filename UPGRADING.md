# Upgrading

## From v2 to v3

The full list of changes is in [CHANGELOG.md](CHANGELOG.md).

### `Credentials` constructor and setters removed

`new Credentials(...)` no longer works: the constructor is private. Use the named constructors, which have been available since 2.1.0:

```php
// v2
new Credentials('API_KEY');
new Credentials('admin', 'somePassword');

// v3
Credentials::legacyHash('API_KEY');
Credentials::password('admin', 'somePassword');
Credentials::accessKey('ACCESS_KEY', 'SECRET_KEY');
```

The setters `setAccessKey()`, `setApiKey()`, `setUser()` and `setPassword()` are gone too, so a `Credentials` object can't be changed once created. To change the credentials of a `Host`, pass new `Credentials` to `Host::setCredentials()`. `Client::simpleFactory()` is unchanged.

### Commands are stateless

A command no longer stores the response it got. `Client::send()` passes the response straight to `process()`, which now takes it as an argument, and `setLastResponse()`, `getResponseText()` and `getStatusCode()` are gone. Code that only calls `send()` or the modules needs no change.

If your command class overrides `process()`, add the response parameter and pass it to the parent:

```php
use Psr\Http\Message\ResponseInterface;

// v2
public function process(): array {
	return array_keys(parent::process());
}

// v3
public function process(ResponseInterface $response): array {
	return array_keys(parent::process($response));
}
```

If it calls `defaultProcess()` or `getResponseCode()`, or the protected `codeException()`, pass the response to those too: `$this->defaultProcess($response)`, `$this->getResponseCode($response)`, `$this->codeException($code, $response)`.

A class that implements `ICommand` directly must drop `setLastResponse()` and declare `process(ResponseInterface $response): mixed`, or a narrower return type.

If you read the raw response with `getResponseText()` or `getStatusCode()` after `send()`, override `process()` in your own command class instead: the body is `(string) $response->getBody()` and the status is `$response->getStatusCode()`. See [Processing the response](docs/commands.md#processing-the-response).

### Command classes grouped by verb removed

The command class names from 2.0, grouped by verb (`Add\User`, `Lists\Users`, …), are gone. Code that only uses the modules needs no change. If you create command objects yourself, switch to the names grouped by module, which have been available since 2.1.0. All classes are in `neto737\HestiaCP\Command`:

```php
// v2
use neto737\HestiaCP\Command\Lists\Users;
$client->send(new Users());

// v3
use neto737\HestiaCP\Command\User\ListUsers;
$client->send(new ListUsers());
```

| v2 | v3 |
| --- | --- |
| `Add\Database` | `Database\AddDatabase` |
| `Add\LetsEncryptDomain` | `Web\AddLetsEncryptDomain` |
| `Add\MailAccount` | `Mail\AddMailAccount` |
| `Add\MailDomain` | `Mail\AddMailDomain` |
| `Add\User` | `User\AddUser` |
| `Add\WebDomain` | `Web\AddWebDomain` |
| `Add\WebDomainFtp` | `Web\AddWebDomainFtp` |
| `Backup\User` | `Backup\BackupUser` |
| `Backup\Users` | `User\BackupUsers` |
| `Change\MailAccountPassword` | `Mail\ChangeMailAccountPassword` |
| `Change\UserPassword` | `User\ChangeUserPassword` |
| `Change\WebDomainFtpPassword` | `Web\ChangeWebDomainFtpPassword` |
| `Change\WebDomainFtpPath` | `Web\ChangeWebDomainFtpPath` |
| `Delete\Database` | `Database\DeleteDatabase` |
| `Delete\Databases` | `Database\DeleteDatabases` |
| `Delete\LetsEncryptDomain` | `Web\DeleteLetsEncryptDomain` |
| `Delete\MailAccount` | `Mail\DeleteMailAccount` |
| `Delete\MailDomain` | `Mail\DeleteMailDomain` |
| `Delete\User` | `User\DeleteUser` |
| `Delete\UserBackup` | `Backup\DeleteUserBackup` |
| `Delete\UserBackupExclusions` | `Backup\DeleteUserBackupExclusions` |
| `Delete\WebDomain` | `Web\DeleteWebDomain` |
| `Delete\WebDomainFtp` | `Web\DeleteWebDomainFtp` |
| `Lists\Database` | `Database\ListDatabase` |
| `Lists\Databases` | `Database\ListDatabases` |
| `Lists\MailAccounts` | `Mail\ListMailAccounts` |
| `Lists\MailDomainDkim` | `Mail\ListMailDomainDkim` |
| `Lists\MailDomainDkimDns` | `Mail\ListMailDomainDkimDns` |
| `Lists\MailDomains` | `Mail\ListMailDomains` |
| `Lists\User` | `User\ListUser` |
| `Lists\UserBackup` | `Backup\ListUserBackup` |
| `Lists\UserBackupExclusions` | `Backup\ListUserBackupExclusions` |
| `Lists\UserBackups` | `Backup\ListUserBackups` |
| `Lists\Users` | `User\ListUsers` |
| `Lists\WebDomains` | `Web\ListWebDomains` |
| `Suspend\MailAccount` | `Mail\SuspendMailAccount` |
| `Suspend\MailDomain` | `Mail\SuspendMailDomain` |
| `Suspend\User` | `User\SuspendUser` |
| `Suspend\WebDomain` | `Web\SuspendWebDomain` |
| `Unsuspend\MailAccount` | `Mail\UnsuspendMailAccount` |
| `Unsuspend\MailDomain` | `Mail\UnsuspendMailDomain` |
| `Unsuspend\User` | `User\UnsuspendUser` |
| `Unsuspend\WebDomain` | `Web\UnsuspendWebDomain` |

### `ARG_*` constants removed

`Command::ARG_1` … `ARG_6` are gone. Only relevant if you wrote your own command classes. Build the request parameters with `positional()`, available since 2.1.0, which takes the arguments in order:

```php
// v2
public function getRequestParams(): array {
	return [self::ARG_1 => $this->user, self::ARG_2 => self::FORMAT_JSON];
}

// v3
public function getRequestParams(): array {
	return $this->positional([$this->user, self::FORMAT_JSON]);
}
```

Use `null` for an optional argument that isn't set. See [Writing your own command](docs/commands.md#writing-your-own-command).

## From v1 to v2

v2 brings the library up to date with HestiaCP 1.10. Most code keeps working, but check each section below. The full list of changes is in [CHANGELOG.md](CHANGELOG.md).

### PHP 8.2 or later

v2 requires PHP 8.2. v1 ran on PHP 7.3+.

The `nette/utils` dependency is gone. If your own code uses Nette Utils, require it in your `composer.json` yourself.

### List and detail methods return arrays

List and detail methods (`Users::list()`, `Webs::listDomains()`, `Backups::listBackups()` and so on) now return plain associative arrays instead of `Nette\Utils\ArrayHash` objects.

```php
// v1
$admin = $client->getModuleUser()->detail('admin');
echo $admin->CONTACT;

// v2
echo $admin['CONTACT'];
```

Search your code for `->` on results of these methods and replace it with array access. `foreach` over the results works as before.

### Exceptions moved to their own namespace

The exception classes moved from `neto737\HestiaCP` to `neto737\HestiaCP\Exceptions`. The class names are unchanged.

```php
// v1
use neto737\HestiaCP\ProcessException;

// v2
use neto737\HestiaCP\Exceptions\ProcessException;
```

This applies to `Exception`, `InvalidArgumentException`, `RuntimeException`, `ProcessException`, `ClientException`, `InvalidResponseException` and `AuthorizationException`. A `catch` with the old name doesn't fail with an error: it simply never matches, so the exception goes uncaught. Search for every `use` and `catch` of these classes.

### Exceptions carry the HestiaCP exit code

In v1, a failed command threw a `ProcessException` with Guzzle's HTTP error message and code `0`, and a server error could surface as a `ClientException`. In v2:

- Exit codes 9 and 10 (wrong credentials, access denied) throw `AuthorizationException`.
- Every other failure throws `ProcessException`, with the HestiaCP exit code as `getCode()` and a readable message.
- `ClientException` means only that the request didn't complete (timeout, DNS, TLS, connection refused).

If you parsed exception messages, compare `$e->getCode()` with the `ResponseCode` constants instead. See [Error handling](docs/errors.md).

### TLS certificates are verified

v1 never checked the server's TLS certificate. v2 checks it by default, so a panel with a self-signed certificate now fails with a `ClientException`.

Either install a valid certificate (for example with Let's Encrypt), or set the `verify` option:

```php
$client = Client::simpleFactory('https://panel.example.com', 'ACCESS_KEY:SECRET_KEY', null, [
	'verify' => '/path/to/ca-bundle.pem', // or false to turn verification off
]);
```

See [TLS certificates](docs/authentication.md#tls-certificates).

### The hostname needs a scheme

The hostname must start with `https://` or `http://` and contain only the host. A hostname without a scheme, or with a port, path, query or fragment, now throws `InvalidArgumentException`.

v1 passed the hostname through without checking it.

```php
// v2 rejects these
Client::simpleFactory('panel.example.com', ...);
Client::simpleFactory('https://panel.example.com:8083/', ...);

// use this instead
Client::simpleFactory('https://panel.example.com', ...);
new Host('https://panel.example.com', $credentials, 8083); // port as a separate argument
```

See [Hostname](docs/authentication.md#hostname).

### `addDomainLetsEncrypt()`: the 4th argument is renamed

The 4th argument of `Webs::addDomainLetsEncrypt()` was called `$restart`, but HestiaCP has always read it as the MAIL flag: `true` also requested a certificate for the mail domain. v2 renames it to `$mail` to match. Code that passes it by position behaves the same as before.

```php
// v1 and v2: certificate for the web and mail domain
$web->addDomainLetsEncrypt('example.com', 'www.example.com', true);

// named arguments must use the new name
$web->addDomainLetsEncrypt('example.com', mail: true);
```

If you passed `true` expecting a service restart, pass `false` instead, unless you also want the mail certificate.

### Backups return `bool`

`Backups::backup()` and `Users::backup()` return `true` when the backup has finished, instead of a string. In v1 these calls failed anyway, because the library tried to parse the backup log as JSON.

### `ResponseCode::E_FORBIDEN` removed

Use the correctly spelled `ResponseCode::E_FORBIDDEN`.

### Custom commands

Only relevant if you wrote your own command classes.

- `getTimeout(): ?float` is now part of `ICommand`. Classes that extend `Command`, `ProcessCommand` or `ListCommand` already have it. Classes that implement `ICommand` directly must add it; return `null` to use the client timeout.
- `processException()` was removed from `ICommand` and `Command`. The client no longer calls it, because HTTP errors no longer throw in Guzzle. Delete any implementation of it.
- `Command::throwCodeException()` was removed. It wasn't used.
- `ICommand::process()` now returns `mixed`. Classes that implement `ICommand` directly must declare a return type, either `mixed` or a narrower one such as `bool` or `array`. `ListCommand::process()` now returns `array`, so subclasses that override it must return `array` too.
- The library is now fully typed. If you extend a library class and override a typed method or redeclare a typed property, use a compatible type: setters in `Client`, `Credentials` and `Host` return `static`, and `Module::$client` is `Client`.

See [Sending commands directly](docs/commands.md).

### Optional: switch to access keys

Not required, but recommended. HestiaCP 1.6+ supports access keys, and password login works only for the admin/root user. v1 code that passed `ACCESS_KEY:SECRET_KEY` as a single string keeps working. v2 also has named constructors:

```php
use neto737\HestiaCP\Authorization\Credentials;

$credentials = Credentials::accessKey('ACCESS_KEY', 'SECRET_KEY');
```

See [Authentication](docs/authentication.md).
