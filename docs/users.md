# Users

```php
$users = $client->getModuleUser();
```

The user module works on all users, so it takes no username.

## Listing

```php
$users->list(); // ['admin' => ['NAME' => '...', 'CONTACT' => '...', ...], ...]

$admin = $users->detail('admin'); // ['NAME' => '...', 'CONTACT' => '...', ...]
echo $admin['CONTACT'];
```

`list()` returns every user, keyed by username. `detail()` returns the fields of one user.

```php
$users->stats('admin'); // ['2026-10-01' => ['U_DISK' => '...', 'U_BANDWIDTH' => '...', ...], ...]
```

`stats()` returns the user's monthly usage statistics, keyed by date.

## Adding

```php
$users->add('other_user', 'pa$$word', 'some@email.com');

// with a package, first name and last name
$users->add('other_user', 'pa$$word', 'some@email.com', 'default', 'John', 'Doe');
```

If the package is `null`, the user gets the `default` package.

## Changing the password, contact email or package

```php
$users->changePassword('other_user', 'otherPa$$word');

$users->changeContact('other_user', 'new@email.com');

$users->changePackage('other_user', 'gold');
$users->changePackage('other_user', 'gold', true); // even if the user's current usage exceeds the package limits
```

To see the available packages, use the [package module](packages.md).

## Suspending and deleting

```php
$users->suspend('other_user');
$users->unsuspend('other_user');

$users->delete('other_user');
```

`suspend()` and `unsuspend()` take an optional second argument, `$restart` (default `false`), that restarts the affected services.

## Backing up all users

```php
$users->backup(); // true when the backup has finished
```

This backs up every user on the server and waits until it's done, with no timeout. To back up one user, use the [backup module](backups.md).

All methods except `list()`, `detail()` and `stats()` return `true` on success and throw an exception on failure (see [Error handling](errors.md)).
