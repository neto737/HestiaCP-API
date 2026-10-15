# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

This will be released as 2.0.0. It brings the library up to date with HestiaCP 1.10 and contains breaking changes, marked **BC break** below.

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
- PHPStan (level 5) and a PHP-CS-Fixer coding-style check, run in CI. `composer check` runs them with the tests.

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

[Unreleased]: https://github.com/neto737/HestiaCP-API/compare/v1.2.0...HEAD
[1.2.0]: https://github.com/neto737/HestiaCP-API/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/neto737/HestiaCP-API/compare/v1.0.1...v1.1.0
[1.0.1]: https://github.com/neto737/HestiaCP-API/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/neto737/HestiaCP-API/releases/tag/v1.0.0
