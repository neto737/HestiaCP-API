# Sending commands directly

The modules are a convenience layer. Each method creates a command object and passes it to `Client::send()`. You can do the same yourself, with the command classes in `neto737\HestiaCP\Command\*`, or with a command class of your own.

## Using a command class

```php
use neto737\HestiaCP\Command\Lists\Users;
use neto737\HestiaCP\Command\Change\UserPassword;

$users = $client->send(new Users()); // array

$client->send(new UserPassword('admin', 'otherPa$$word')); // true
```

`send()` returns whatever the command's `process()` method returns. The raw response body is kept on the command object:

```php
$command = new Users();
$client->send($command);

echo $command->getResponseText(); // the JSON returned by HestiaCP
echo $command->getStatusCode();   // the HTTP status code
```

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
		return [
			'arg1' => $this->user,
			'arg2' => $this->min,
			'arg3' => $this->hour,
			'arg4' => $this->day,
			'arg5' => $this->month,
			'arg6' => $this->weekday,
			'arg7' => $this->command,
		];
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
		return [
			'arg1' => $this->user,
			'arg2' => 'json',
		];
	}
}

$jobs = $client->send(new ListCronJobs('admin'));
```

Most HestiaCP list scripts print JSON only when you pass `json` as their format argument.

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
