# AGENTS.md

Instructions for AI coding agents working **on** this library. To learn how to **use** it, read [README.md](README.md) and [docs/](docs/).

`neto737/hestiacp-api` is a PHP 8.3+ client for the HestiaCP API. Namespace `neto737\HestiaCP\` maps to `src/HestiaCP/`. The only runtime dependency is Guzzle. Behavior is tested against HestiaCP 1.10.9.

## Before you hand over

Run both, in this order. Both must pass.

```sh
composer cs-fix   # fixes the coding style in place
composer check    # coding-style check, PHPStan (level 8) and PHPUnit
```

Don't run `composer test:live` unless the user asks: it needs a real HestiaCP server (see [tests/Live/LiveTestCase.php](tests/Live/LiveTestCase.php)) and creates and deletes users on it.

## Layout

| Path | What it holds |
| --- | --- |
| `src/HestiaCP/Client.php` | `send(ICommand)`, `testAuthorization()`, `getModule*()` |
| `src/HestiaCP/Authorization/` | `Credentials` (named constructors only) and `Host` (immutable) |
| `src/HestiaCP/Command/<Module>/` | One class per HestiaCP script, for example `Command/Web/AddWebDomainAlias.php` runs `v-add-web-domain-alias` |
| `src/HestiaCP/Command/` | Base classes `Command`, `ProcessCommand` (returns `bool`), `ListCommand` (returns `array`); `RawCommand`, `RawListCommand`, `MakeTmpFile`, `TestAuthorization`, `ResponseCode` |
| `src/HestiaCP/Module/` | `Users`, `Webs`, `Mails`, `Databases`, `Backups`, `Packages`, `Crons`, `Dns`: thin wrappers, each method builds one command and calls `$this->client->send()` |
| `src/HestiaCP/Exceptions/` | Exception hierarchy, described in [docs/errors.md](docs/errors.md) |
| `tests/Command/<Module>Test.php` | Per command: script name, request params, `needReturnCode()` |
| `tests/Module/ModuleSendTest.php` | Every module method through a mocked client: the request it sends and the value it returns |
| `tests/SensitiveParameterTest.php` | Every secret parameter is marked `#[\SensitiveParameter]` |
| `tests/Live/` | Suite against a real server (`composer test:live`) |
| `docs/<module>.md` | User docs, one file per module |

## Code style

`composer cs-fix` enforces most of this, but write it right the first time:

- Tabs for indentation, LF line endings, `declare(strict_types=1);` in every file.
- Opening braces on the same line, for classes and functions too, with a blank line after a class's opening brace.
- Single quotes, short array syntax, no trailing comma in single-line arrays.
- Constructor property promotion with `private readonly` for command arguments.
- `#[\Override]` on methods that override a parent implementation, such as `process()`. Typed class constants (`const string`).
- `#[\SensitiveParameter]` on every password, key or secret, in both the command constructor and the module method.

## Adding a HestiaCP command

Follow every step. Skipping the tests or docs is the most common mistake.

1. **Check the script's arguments** in the [HestiaCP CLI reference](https://hestiacp.com/docs/reference/cli) or the script's source (`bin/v-*` in the HestiaCP repository). HestiaCP reads arguments by position, so the order must match exactly.
2. **Create the command class** in `src/HestiaCP/Command/<Module>/`. The name is the script name without `v-`, in PascalCase, with these compound words split: `LetsEncrypt`, `HttpAuth`, `SslCert`, `SslHome` (`v-change-web-domain-httpauth` is `ChangeWebDomainHttpAuth`). Extend `ProcessCommand` when the script changes something, `ListCommand` when it prints JSON.
3. **Build the arguments** in `getRequestParams()` with `$this->positional([...])`, in the script's order:
   - Optional arguments are nullable and default to `null`. A trailing `null` is dropped; a `null` before a set argument is sent as `''`.
   - Booleans go through `$this->convertBool()` (`'yes'`/`'no'`). For an optional `?bool $restart = null`, write `$this->restart === null ? null : $this->convertBool($this->restart)`, so `null` leaves the choice to HestiaCP.
   - List commands pass `self::FORMAT_JSON` as the format argument. A command that returns one object overrides `process()` and returns `$this->convertDetail(parent::process($response))`.
   - Some scripts read `''` differently from a missing argument, or treat any value, `no` included, as yes. Check the script's source and document the behavior.
4. **Add the module method** in `src/HestiaCP/Module/<Module>.php`. User-scoped modules pass `$this->user` as the first command argument. Give it a docblock with `@param` notes for non-obvious arguments and `@throws` for `ClientException` and `ProcessException`.
5. **Add the tests:**
   - `tests/Command/<Module>Test.php`: the script name, the exact `getRequestParams()` array, and `needReturnCode()`.
   - `tests/Module/ModuleSendTest.php`: a case in the matching data provider. Use distinct values and non-default booleans, so a swapped or dropped argument fails.
   - `tests/SensitiveParameterTest.php`: a row for the command and the module method if they take a secret.
   - `tests/Live/<Module>LiveTest.php`, when the method can run on a test server without side effects outside the test user.
6. **Document it** in `docs/<module>.md` (see [Docs style](#docs-style)), and in the README modules table if it adds a new area.
7. **Add a CHANGELOG entry** under `## [Unreleased]`, in the existing format: ``- Web: `addDomainRedirect()` and `deleteDomainRedirect()` (`Web\AddWebDomainRedirect`, `Web\DeleteWebDomainRedirect`; `v-add-web-domain-redirect`, `v-delete-web-domain-redirect`).``
8. Run `composer cs-fix` and `composer check`.

## Behavior to keep in mind

- **Commands are stateless.** `process(ResponseInterface $response)` receives the response; never store it on the command. Modules hold no state either, and each `getModule*()` call returns a new instance.
- **Exit codes depend on the credentials.** With an access key, HestiaCP sends the exit code in the `Hestia-Exit-Code` header and the body holds the output or error message. With a legacy API key or password, a `ProcessCommand` gets the exit code as the body (`returncode=yes`), and a failed list command answers HTTP 200 with `Error: ...`, which becomes a `ProcessException` with code `0`.
- **Exit code 21** means `E_BACKUP` for backup scripts and `E_API_DISABLED` for the API; both constants are `21`.
- **The client doesn't follow redirects** and turns off Guzzle's HTTP errors; the commands interpret status codes themselves.
- **Timeouts:** a command's `getTimeout()` can raise the client timeout, never lower it. `0` means no timeout.
- **HestiaCP bugs:** when HestiaCP 1.10.9 misbehaves, don't work around it silently. Document it in the module doc, as [docs/packages.md](docs/packages.md#copying-renaming-and-deleting) and [docs/dns.md](docs/dns.md#dnssec) do.

## Docs style

- Every public module method appears in `docs/<module>.md` as a call in a `php` block, with a trailing comment showing the return value or what an argument means.
- Plain, short sentences in the present tense, addressed to the reader as "you". State facts and defaults; skip filler and marketing words.
- Say what `null` and the defaults do, and which HestiaCP exit code a predictable failure gives (for example `ResponseCode::E_EXISTS`).
- Link between docs with relative paths and heading anchors.

## Breaking changes

Mark them **BC break** in the CHANGELOG and add a migration section to [UPGRADING.md](UPGRADING.md). Public method and parameter names are part of the API, because callers use named arguments.

## Commits

- Commit only when the user asks.
- One logical change per commit. Subject in the past tense, sentence case, no trailing period: `Added web domain redirects`, `Fixed legacy list command errors reported as invalid responses`, `Documented ...`.
- Don't add AI co-author trailers.
- Don't commit `ROADMAP.md`; it holds the maintainer's local notes.
