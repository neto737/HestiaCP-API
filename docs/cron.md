# Cron jobs

```php
$cron = $client->getModuleCron('admin');
```

The cron module works on the cron jobs of one user, given when you get the module. Jobs are identified by their numeric ID.

## Listing

```php
$cron->listJobs(); // [7 => ['MIN' => '0', 'HOUR' => '3', 'CMD' => '...', 'SUSPENDED' => 'no', ...], ...]

$cron->listJob(7); // the fields of one job
```

## Adding

The schedule takes the five crontab fields as strings: minute, hour, day of month, month, day of week.

```php
$cron->addJob('0', '3', '*', '*', '*', '/usr/bin/php /home/admin/cron.php'); // every day at 03:00

$cron->addJob('*/5', '*', '*', '*', '1-5', 'uptime', 20); // with job ID 20
```

Without a job ID, HestiaCP gives the job the next free ID. `addJob()` returns `true`, not the ID, so call `listJobs()` to find it, or pass your own.

## Changing

```php
$cron->changeJob(7, '30', '4', '*', '*', '0', '/usr/bin/php /home/admin/cron.php');
```

`changeJob()` replaces the whole schedule and command, so pass every field.

## Suspending and deleting

```php
$cron->suspendJob(7);
$cron->unsuspendJob(7);

$cron->deleteJob(7);
```

## Restarting the cron service

`addJob()`, `suspendJob()`, `unsuspendJob()` and `deleteJob()` take an optional last argument, `$restart`:

- `null` (default) leaves it to HestiaCP. `unsuspendJob()` restarts the cron service by default; the others restart it only when the server is set up for scheduled restarts.
- `true` restarts the cron service right away.
- `false` doesn't restart it.

```php
$cron->deleteJob(7, false);
```

All methods except `listJobs()` and `listJob()` return `true` on success and throw an exception on failure (see [Error handling](errors.md)).
