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
```

All methods except the `list*()` methods return `true` on success and throw an exception on failure (see [Error handling](errors.md)).
