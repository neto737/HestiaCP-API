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

## Adding

```php
$users->add('other_user', 'pa$$word', 'some@email.com');

// with a package, first name and last name
$users->add('other_user', 'pa$$word', 'some@email.com', 'default', 'John', 'Doe');
```

If the package is `null`, the user gets the `default` package.

## Changing the password

```php
$users->changePassword('other_user', 'otherPa$$word');
```

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

All methods except `list()` and `detail()` return `true` on success and throw an exception on failure (see [Error handling](errors.md)).
