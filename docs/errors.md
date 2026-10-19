# Error handling

Every failure is thrown as an exception. All of them extend `\neto737\HestiaCP\Exceptions\Exception`.

```php
use neto737\HestiaCP\Command\ResponseCode;
use neto737\HestiaCP\Exceptions\AuthorizationException;
use neto737\HestiaCP\Exceptions\ClientException;
use neto737\HestiaCP\Exceptions\ProcessException;

try {
	$client->getModuleUser()->add('other_user', 'pa$$word', 'some@email.com');
} catch (AuthorizationException $e) {
	// bad credentials, or not allowed to run the command (exit code 9 or 10)
} catch (ProcessException $e) {
	if ($e->getCode() === ResponseCode::E_EXISTS) {
		// the user already exists
	}
} catch (ClientException $e) {
	// connection problem (timeout, DNS, TLS, ...), or the host isn't HestiaCP
}
```

Catch `AuthorizationException` before `ProcessException`, because it is a subclass. Likewise, catch `InvalidResponseException` before `ClientException` to tell a host that isn't HestiaCP apart from a connection problem.

## Exception types

All classes are in the `neto737\HestiaCP\Exceptions` namespace.

| Exception | Extends | Thrown when |
| --- | --- | --- |
| `AuthorizationException` | `ProcessException` | The credentials are wrong (exit code 9) or the user may not run the command (exit code 10). |
| `InvalidResponseException` | `ClientException` | The response isn't what HestiaCP sends, for example when the hostname points to another server. |
| `ProcessException` | `RuntimeException` | HestiaCP ran the command and it failed with any other exit code, or a list command returned invalid JSON. |
| `ClientException` | `RuntimeException` | The request didn't complete: timeout, DNS failure, TLS error, refused connection. The original Guzzle exception is available with `getPrevious()`. |
| `InvalidArgumentException` | `Exception` | An argument is invalid before anything is sent, for example a malformed hostname or a `RawCommand` name that isn't a HestiaCP script name. |

## Exit codes

For `ProcessException` and its subclasses, `getCode()` returns the HestiaCP exit code. The codes are constants on `\neto737\HestiaCP\Command\ResponseCode`:

| Code | Constant | Meaning |
| --- | --- | --- |
| 1 | `E_ARGS` | Not enough arguments provided |
| 2 | `E_INVALID` | Object or argument is not valid |
| 3 | `E_NOTEXIST` | Object doesn't exist |
| 4 | `E_EXISTS` | Object already exists |
| 5 | `E_SUSPENDED` | Object is suspended |
| 6 | `E_UNSUSPENDED` | Object is already unsuspended |
| 7 | `E_INUSE` | Object can't be deleted because another object uses it |
| 8 | `E_LIMIT` | Hosting package limits reached |
| 9 | `E_PASSWORD` | Wrong password |
| 10 | `E_FORBIDDEN` | Object cannot be accessed by the user |
| 11 | `E_DISABLED` | Subsystem is disabled |
| 12 | `E_PARSING` | Configuration is broken |
| 13 | `E_DISK` | Not enough disk space |
| 14 | `E_LA` | Server is too busy |
| 15 | `E_CONNECT` | Connection failed, host is unreachable |
| 16 | `E_FTP` | FTP server is not responding |
| 17 | `E_DB` | Database server is not responding |
| 18 | `E_RRD` | RRDtool failed to update the database |
| 19 | `E_UPDATE` | Update operation failed |
| 20 | `E_RESTART` | Service restart failed |
| 21 | `E_BACKUP` / `E_API_DISABLED` | Backup failed, or API access is disabled for this user |

HestiaCP uses 21 for two things: `E_BACKUP` in its scripts and `E_API_DISABLED` in the API. Both constants have the value 21.

An unknown code gets the message `Unknown error (exit code N)`. `ResponseCode::getMessage($code)` returns the message for any code.
