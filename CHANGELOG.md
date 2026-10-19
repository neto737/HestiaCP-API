# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed

- **BC break:** commands are stateless. `ICommand::process()` takes the response, `process(ResponseInterface $response): mixed`, and `Client::send()` passes it in, instead of storing it on the command with `setLastResponse()` first. Sending the same command instance twice no longer overwrites the first result. `Command::defaultProcess()` and `Command::getResponseCode()` take the response too, as does the protected `Command::codeException()`. Classes that implement `ICommand` or override `process()` must update their signatures.

### Removed

- **BC break:** `ICommand::setLastResponse()`, and `Command::getResponseText()` and `Command::getStatusCode()`. A command no longer keeps the response; override `process()` to read it.

- **BC break:** the `Credentials` constructor and its setters (`setAccessKey()`, `setApiKey()`, `setUser()`, `setPassword()`), deprecated since 2.1.0. The constructor is private: use `Credentials::accessKey()`, `Credentials::legacyHash()` or `Credentials::password()`. `Credentials` is immutable; to change the credentials of a `Host`, pass new `Credentials` to `Host::setCredentials()`.

## [2.1.0] - 2026-10-18

### Added

- `RawCommand` and `RawListCommand` run any HestiaCP script by name, for scripts with no command class: `new RawCommand('v-add-firewall-rule', ['DROP', '203.0.113.7', '25'])` returns `true`, and `new RawListCommand('v-list-firewall', ['json'])` returns the decoded JSON. A name that isn't a HestiaCP script name throws an `InvalidArgumentException`.
- Databases: `changePassword()`, `suspend()` and `unsuspend()`, with the command classes `ChangeDatabasePassword`, `SuspendDatabase` and `UnsuspendDatabase` (`v-change-database-password`, `v-suspend-database`, `v-unsuspend-database`).
- Users: `changeContact()`, `changePackage()` and `stats()`, with the command classes `ChangeUserContact`, `ChangeUserPackage` and `ListUserStats` (`v-change-user-contact`, `v-change-user-package`, `v-list-user-stats`).
- Package module, `Client::getModulePackage()`: `list()` and `detail()`, with the command classes `Package\ListUserPackages` and `Package\ListUserPackage` (`v-list-user-packages`, `v-list-user-package`).
- Cron module, `Client::getModuleCron($user)`: `listJobs()`, `listJob()`, `addJob()`, `changeJob()`, `suspendJob()`, `unsuspendJob()` and `deleteJob()`, with the command classes in `Command\Cron` (`v-list-cron-jobs`, `v-list-cron-job`, `v-add-cron-job`, `v-change-cron-job`, `v-suspend-cron-job`, `v-unsuspend-cron-job`, `v-delete-cron-job`). The optional `$restart` argument is only sent when set, so each script keeps HestiaCP's own default.
- Web: `listDomain()`, aliases (`addDomainAlias()`, `deleteDomainAlias()`), SSL (`listDomainSsl()`, `addDomainSsl()`, `changeDomainSslCert()`, `changeDomainSslHome()`, `deleteDomainSsl()`, `addDomainSslForce()`, `deleteDomainSslForce()`, `addDomainSslHsts()`, `deleteDomainSslHsts()`), templates (`listTemplates()`, `listBackendTemplates()`, `listProxyTemplates()`, `changeDomainTemplate()`, `changeDomainBackendTemplate()`, `changeDomainProxyTemplate()`) and the proxy (`addDomainProxy()`, `deleteDomainProxy()`), with the command classes in `Command\Web` (`v-list-web-domain`, `v-add-web-domain-alias`, `v-delete-web-domain-alias`, `v-list-web-domain-ssl`, `v-add-web-domain-ssl`, `v-change-web-domain-sslcert`, `v-change-web-domain-sslhome`, `v-delete-web-domain-ssl`, `v-add-web-domain-ssl-force`, `v-delete-web-domain-ssl-force`, `v-add-web-domain-ssl-hsts`, `v-delete-web-domain-ssl-hsts`, `v-list-web-templates`, `v-list-web-templates-backend`, `v-list-web-templates-proxy`, `v-change-web-domain-tpl`, `v-change-web-domain-backend-tpl`, `v-change-web-domain-proxy-tpl`, `v-add-web-domain-proxy`, `v-delete-web-domain-proxy`). Like the cron module, the optional `$restart` argument is only sent when set. The proxy methods throw an `InvalidArgumentException` when `$restart` is set without `$extensions`, because HestiaCP would read the empty extensions as none.
- Mail: `listDomain()`, `listAccount()`, aliases (`addAccountAlias()`, `deleteAccountAlias()`), forwarding (`addAccountForward()`, `deleteAccountForward()`, `addAccountForwardOnly()`, `deleteAccountForwardOnly()`) and autoreplies (`listAccountAutoreply()`, `addAccountAutoreply()`, `deleteAccountAutoreply()`), with the command classes in `Command\Mail` (`v-list-mail-domain`, `v-list-mail-account`, `v-add-mail-account-alias`, `v-delete-mail-account-alias`, `v-add-mail-account-forward`, `v-delete-mail-account-forward`, `v-add-mail-account-fwd-only`, `v-delete-mail-account-fwd-only`, `v-list-mail-account-autoreply`, `v-add-mail-account-autoreply`, `v-delete-mail-account-autoreply`).
- DNS module, `Client::getModuleDns($user)`: domains (`listDomains()`, `listDomain()`, `addDomain()`, `deleteDomain()`, `suspendDomain()`, `unsuspendDomain()`, `changeDomainIp()`, `changeDomainTtl()`, `changeDomainExpiration()`, `changeDomainSoa()`, `changeDomainTemplate()`), DNSSEC (`changeDomainDnssec()`, `listDnssecPublicKey()`), records (`listRecords()`, `addRecord()`, `changeRecord()`, `changeRecordId()`, `suspendRecord()`, `unsuspendRecord()`, `deleteRecord()`) and templates (`listTemplates()`, `listTemplate()`), with the command classes in `Command\Dns` (`v-list-dns-domains`, `v-list-dns-domain`, `v-add-dns-domain`, `v-delete-dns-domain`, `v-suspend-dns-domain`, `v-unsuspend-dns-domain`, `v-change-dns-domain-ip`, `v-change-dns-domain-ttl`, `v-change-dns-domain-exp`, `v-change-dns-domain-soa`, `v-change-dns-domain-tpl`, `v-change-dns-domain-dnssec`, `v-list-dnssec-public-key`, `v-list-dns-records`, `v-add-dns-record`, `v-change-dns-record`, `v-change-dns-record-id`, `v-suspend-dns-record`, `v-unsuspend-dns-record`, `v-delete-dns-record`, `v-list-dns-templates`, `v-list-dns-template`). Like the cron module, the optional `$restart` argument is only sent when set. `addDomain()` takes the nameservers as an array and throws an `InvalidArgumentException` for exactly one, which HestiaCP would ignore, or more than eight.
- `Command::positional()` builds the `arg1`, `arg2`, … request parameters from a list of arguments. A `null` argument is left out when nothing after it is set and sent as an empty string otherwise. Every built-in command uses it, and it has no limit on the number of arguments.
- `ICommand` is generic for static analysis (`ICommand<T>`, `process()` returns `T`), and `Client::send()` returns the command's result type. `ProcessCommand` is an `ICommand<bool>` and `ListCommand` an `ICommand<array<mixed>>`. Custom commands that extend `Command` directly can declare theirs with `@extends Command<...>`. Docblocks only, nothing changes at runtime.

### Changed

- The command classes are named after the HestiaCP script they run, and grouped by the module that sends them: `v-add-user` is `Command\User\AddUser`, `v-list-mail-accounts` is `Command\Mail\ListMailAccounts`, `v-add-letsencrypt-domain` is `Command\Web\AddLetsEncryptDomain`. Class names no longer collide across namespaces, and the script gives the class name. The constructors are unchanged.
- `AddWebDomain` and `AddWebDomainFtp` no longer send an empty last argument when `proxyExtensions` or `ftpPath` isn't set. HestiaCP treats both the same way.
- `Client::simpleFactory()` splits `ACCESS_KEY:SECRET_KEY` into an access key and secret key, so the credentials report access-key mode and the request sends `access_key` and `secret_key` instead of `hash`. HestiaCP already handled both forms the same way. A value with no colon, or more than one, is still a legacy API key.
- `TestAuthorization` now extends `ProcessCommand` instead of `Command`. Its behaviour is unchanged.
- The command classes use constructor property promotion with `readonly` properties. Their constructors take the same arguments as before.
- `Credentials` stores its mode (access key, legacy hash or password) explicitly instead of inferring it from which fields are `null`. The named constructors no longer go through the public constructor. `setApiKey()` and `setAccessKey()` now also clear the user and password, so `getUser()` and `getPassword()` return `null` after switching away from password mode. Request parameters are unchanged.

### Deprecated

- The command class names grouped by verb (`Add\User`, `Lists\Users`, `Suspend\MailAccount`, …). Each is now an empty subclass of its new name, so existing code keeps working and IDEs and static analysis flag the old name. Each old file names its replacement (`Lists\Users` becomes `User\ListUsers`).
- `Command::ARG_1` … `ARG_6`. Use `positional()` instead.
- The `Credentials` constructor, where a `null` password means a legacy API key, and its setters (`setAccessKey()`, `setApiKey()`, `setUser()`, `setPassword()`). Use `Credentials::accessKey()`, `Credentials::legacyHash()` or `Credentials::password()` instead. `Client::simpleFactory()` uses the named constructors and takes the same arguments as before.

## [2.0.0] - 2026-10-15

This release brings the library up to date with HestiaCP 1.10 and contains breaking changes, marked **BC break** below. See [UPGRADING.md](UPGRADING.md) for how to migrate from v1.

### Added

- Database module (`Client::getModuleDatabase()`): `add()`, `delete()`, `deleteDatabases()`, `listDatabase()` and `listDatabases()`.
- Access-key authentication (HestiaCP 1.6+). Named constructors `Credentials::accessKey($key, $secret)`, `Credentials::legacyHash($hash)` and `Credentials::password($user, $pass)`. `ACCESS_KEY:SECRET_KEY` can also be passed as a single string to `Client::simpleFactory()`.
- `verify` client option: `true` (default), `false`, or a path to a CA bundle.
- Missing exit codes in `ResponseCode` (`E_BACKUP`, `E_API_DISABLED`, `E_FORBIDDEN`, and others), with a generic message for unknown codes.
- `Add\LetsEncryptDomain`: `mail` and `keyAlgo` arguments (`ecdsa-256`, `ecdsa-384` or `rsa`). `keyAlgo` is sent only when set, because HestiaCP 1.10.5 doesn't support it yet.
- `Add\User`: optional `name` and `lastname` arguments.
- `Delete\UserBackupExclusions`: optional `system` argument.
- Tests for the client and command processing, using Guzzle's `MockHandler`.
- GitHub Actions CI on PHP 8.2 to 8.5, plus a lowest-dependencies run on PHP 8.2.
- PHPStan (level 6) and a PHP-CS-Fixer coding-style check, run in CI. `composer check` runs them with the tests.

### Changed

- **BC break:** requires PHP 8.2 or later. PHP 8.1 reached end of life on 31 December 2025.
- **BC break:** list and detail methods return plain associative arrays instead of `Nette\Utils\ArrayHash`. Replace `$item->FIELD` with `$item['FIELD']`.
- **BC break:** exceptions moved from `neto737\HestiaCP\*` to `neto737\HestiaCP\Exceptions\*`, one class per file. Autoloading switched from `classmap` to PSR-4.
- **BC break:** TLS certificates are verified by default. For a self-signed certificate, pass `'verify' => false` or a CA bundle path.
- **BC break:** the hostname must include an `http://` or `https://` scheme. A port, path, query, fragment or user info in it throws `InvalidArgumentException`. A trailing slash is removed, and the scheme and host are lowercased.
- **BC break:** the 4th argument of `Webs::addDomainLetsEncrypt()` is renamed from `$restart` to `$mail`, which is how HestiaCP has always read it. Positional calls behave the same; named arguments must use the new name.
- **BC break:** `getTimeout()` is part of `ICommand`. Classes that implement `ICommand` directly must add it.
- Errors are read from HestiaCP's HTTP status and `Hestia-Exit-Code` header instead of Guzzle exceptions. Exit codes 9 and 10 throw `AuthorizationException`; other non-zero codes throw `ProcessException`; network and timeout failures throw `ClientException` with the original exception as `previous`.
- **BC break:** `Backups::backup()` and `Users::backup()` return `bool` instead of `string`. The backup scripts print a plain-text log, not JSON, so the old return value could not be parsed.
- `Databases::listDatabases()` returns every database instead of only the first one.
- `Add\User` sends a null package as `default`, so the arguments after it keep their positions.
- `Credentials` getters return `?string`.
- Guzzle requirement is `^7.15.2 || ^8.2`. PHPUnit is `^11.5 || ^12.0`.
- `minimum-stability` is `stable` instead of `dev`.
- Implicit nullable parameters (`string $x = null`) replaced with `?string`.
- Every file declares `strict_types=1`. Properties have native types instead of `@var` docblocks.
- **BC break:** `ICommand::process()` returns `mixed`. `ListCommand::process()` returns `array`. The setters in `Client`, `Credentials` and `Host` return `static`. `Module::$client` is typed `Client` and `ResponseCode::$messages` is typed `array`. Custom classes that implement or override these must declare compatible types.
- Documentation links point to `hestiacp.com/docs`.

### Removed

- **BC break:** `nette/utils` dependency.
- **BC break:** `ICommand::processException()` and `Command::processException()`, the unused `Command::throwCodeException()`, and the misspelled `ResponseCode::E_FORBIDEN` alias (use `E_FORBIDDEN`).
- VestaCP-era JSON workarounds in `Lists\MailDomainDkim` and `Lists\MailDomainDkimDns`. HestiaCP outputs valid JSON for both.
- Travis CI configuration.

### Fixed

- `TestAuthorization` sent an empty command, which current HestiaCP rejects. It now runs `v-list-sys-info` and returns `false` on an authentication failure.
- `Backup\User` and `Backup\Users` failed because they expected JSON. `Backup\Users` also sent a `json` argument the script doesn't take.
- PHP 8.1 deprecation warnings from passing `null` to `preg_match()` and `preg_replace()`.
- `getModuleMail()`, `getModuleWeb()`, `getModuleBackup()` and `getModuleDatabase()` cached one module per class, so asking for a second user returned the first user's module and ran its calls against the first user's account. Modules are now cached per user.

## [1.2.0] - 2021-10-13

### Added

- Backup module (`Client::getModuleBackup()`): create, delete and list backups, and list and delete backup exclusions.
- `Users::backup()` to back up all users.

## [1.1.0] - 2021-09-10

### Added

- API key authentication: pass the key as the only credential to `Client::simpleFactory()` or `Credentials`.

## [1.0.1] - 2021-07-01

### Fixed

- `Unsuspend\MailAccount` called `v-suspend-mail-account` instead of `v-unsuspend-mail-account`.

## [1.0.0] - 2021-06-30

First release, with the user, web and mail modules.

[Unreleased]: https://github.com/neto737/HestiaCP-API/compare/v2.1.0...HEAD
[2.1.0]: https://github.com/neto737/HestiaCP-API/compare/v2.0.0...v2.1.0
[2.0.0]: https://github.com/neto737/HestiaCP-API/compare/v1.2.0...v2.0.0
[1.2.0]: https://github.com/neto737/HestiaCP-API/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/neto737/HestiaCP-API/compare/v1.0.1...v1.1.0
[1.0.1]: https://github.com/neto737/HestiaCP-API/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/neto737/HestiaCP-API/releases/tag/v1.0.0
