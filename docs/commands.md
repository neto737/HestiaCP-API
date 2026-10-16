# Sending commands directly

The modules are a convenience layer. Each method creates a command object and passes it to `Client::send()`. You can do the same yourself, with the command classes in `neto737\HestiaCP\Command`, or with a command class of your own.

## Using a command class

Each class is named after the HestiaCP script it runs: drop the `v-` prefix and PascalCase the rest. `v-list-users` is `ListUsers`, `v-change-user-password` is `ChangeUserPassword`, and `v-add-letsencrypt-domain` is `AddLetsEncryptDomain`.

The classes are grouped by the module that sends them: `Command\User`, `Command\Web`, `Command\Mail`, `Command\Database`, `Command\Backup` and `Command\Package`. For example, `v-add-letsencrypt-domain` is sent by the web module, so its class is `Command\Web\AddLetsEncryptDomain`.

```php
use neto737\HestiaCP\Command\User\ChangeUserPassword;
use neto737\HestiaCP\Command\User\ListUsers;

$users = $client->send(new ListUsers()); // array

$client->send(new ChangeUserPassword('admin', 'otherPa$$word')); // true
```

The names grouped by verb from 2.0 (`Lists\Users`, `Change\UserPassword`, …) still work, but are deprecated and will be removed in 3.0.

`send()` returns whatever the command's `process()` method returns. The raw response body is kept on the command object:

```php
$command = new ListUsers();
$client->send($command);

echo $command->getResponseText(); // the JSON returned by HestiaCP
echo $command->getStatusCode();   // the HTTP status code
```

## Running any script

For a script that has no command class, use `RawCommand` or `RawListCommand` with the script name and its arguments, in order:

```php
use neto737\HestiaCP\Command\RawCommand;
use neto737\HestiaCP\Command\RawListCommand;

$client->send(new RawCommand('v-add-cron-job', ['admin', '0', '3', '*', '*', '*', '/usr/bin/php /home/admin/cron.php'])); // true

$jobs = $client->send(new RawListCommand('v-list-cron-jobs', ['admin', 'json'])); // array
```

- `RawCommand` is for scripts that change something. Like `ProcessCommand`, it returns `true` on exit code 0 and throws an exception otherwise.
- `RawListCommand` is for scripts that print JSON. Like `ListCommand`, it returns the decoded array. Most list scripts print JSON only when you pass `json` as their format argument, so include it in the arguments.

The arguments go through `positional()` (see below), so `null` works the same way for optional arguments. A name that isn't a HestiaCP script name (`v-` followed by lowercase words joined by dashes) throws an `InvalidArgumentException` before anything is sent.

## Writing your own command

Extend one of the two base classes:

- `ProcessCommand` for commands that change something. `process()` returns `true` on exit code 0 and throws an exception otherwise.
- `ListCommand` for commands that return JSON. `process()` returns the decoded array.

Implement `getName()` (the HestiaCP script name) and `getRequestParams()` (the script's arguments, in order):

```php
use neto737\HestiaCP\Command\ProcessCommand;

class AddCronJob extends ProcessCommand {

	public function __construct(
		private string $user,
		private string $min,
		private string $hour,
		private string $day,
		private string $month,
		private string $weekday,
		private string $command
	) {}

	public function getName(): string {
		return 'v-add-cron-job';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->min,
			$this->hour,
			$this->day,
			$this->month,
			$this->weekday,
			$this->command,
		]);
	}
}

$client->send(new AddCronJob('admin', '0', '3', '*', '*', '*', '/usr/bin/php /home/admin/cron.php'));
```

```php
use neto737\HestiaCP\Command\ListCommand;

class ListCronJobs extends ListCommand {

	public function __construct(private string $user) {}

	public function getName(): string {
		return 'v-list-cron-jobs';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, 'json']);
	}
}

$jobs = $client->send(new ListCronJobs('admin'));
```

Most HestiaCP list scripts print JSON only when you pass `json` as their format argument.

`positional()` turns the list into the `arg1`, `arg2`, … parameters HestiaCP expects. HestiaCP reads the arguments by position, so use `null` for an optional argument that isn't set: it is left out when nothing after it is set, and sent as an empty string otherwise, so the arguments after it keep their positions.

```php
$this->positional(['admin', null, 'yes']); // ['arg1' => 'admin', 'arg2' => '', 'arg3' => 'yes']
$this->positional(['admin', 'yes', null]); // ['arg1' => 'admin', 'arg2' => 'yes']
```

The `ARG_1` … `ARG_6` constants are deprecated and will be removed in 3.0.

### Timeouts

To give a slow command more time than the client default, override `getTimeout()`:

```php
public function getTimeout(): ?float {
	return 120; // seconds; 0 means no timeout, null uses the client timeout
}
```

A command can raise the timeout but never lower it below the client's.

### Implementing `ICommand` directly

You can implement `\neto737\HestiaCP\Command\ICommand` without extending the base classes. You then have to implement every method yourself, including `setLastResponse()`, `needReturnCode()`, `process()` and `getTimeout()`. Extending `ProcessCommand` or `ListCommand` is simpler, and they already handle HestiaCP's exit codes and error responses.

The full list of HestiaCP commands and their arguments is in the [HestiaCP CLI reference](https://hestiacp.com/docs/reference/cli).
