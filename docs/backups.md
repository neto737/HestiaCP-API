# Backups

```php
$backup = $client->getModuleBackup('admin');
```

The backup module works on the backups of one user, given when you get the module. To back up every user at once, use `Users::backup()` in the [user module](users.md).

## Creating a backup

```php
$backup->backup(); // true when the backup has finished

$backup->backup(false); // don't send the user a notification
```

The call waits until the backup is done, with no timeout, so it can take a long time on a large account.

## Listing

```php
$backup->listBackups(); // ['admin.2021-10-13_18-12-53.tar' => [...], ...]

$backup->listBackup('admin.2021-10-13_18-12-53.tar'); // the fields of one backup
```

## Deleting

```php
$backup->delete('admin.2021-10-13_18-12-53.tar');
```

## Exclusions

Exclusions are the parts of the account left out of backups.

```php
$backup->listBackupExclusions();

$backup->deleteExclusions();      // delete all exclusions
$backup->deleteExclusions('WEB'); // delete one type: WEB, DNS, MAIL, DB, CRON or USER

// replace the whole list; the types left out are cleared
$backup->updateExclusions([
	'WEB' => ['example.com:public_html/cache', 'example.net'], // a domain, or domain:path inside it
	'MAIL' => ['example.com:info'],                            // a domain, or domain:account
	'DNS' => ['example.com'],
	'DB' => ['alice_wordpress'],
	'USER' => ['tmp'],                                         // a directory in the user's home
	'CRON' => ['*'],                                           // CRON only accepts *
]);
```

In every type, `*` excludes everything of that type. An entry can't be empty or contain a quote, a comma or whitespace: `updateExclusions()` throws an `InvalidArgumentException` for those, or for an unknown type, before sending anything. HestiaCP checks the rest, and a `ProcessException` with `ResponseCode::E_INVALID` tells you an entry was refused.

`updateExclusions()` sends two requests. HestiaCP's `v-update-user-backup-exclusions` reads the list from a file, so `v-make-tmp-file` first writes it to `/tmp/hestiacp-api-backup-exclusions-<user>` on the server. The file stays there, and the next call for the same user replaces it. With a non-admin user's access key, the key needs permission to run both commands.

All methods except the `list*()` methods return `true` on success and throw an exception on failure (see [Error handling](errors.md)).
