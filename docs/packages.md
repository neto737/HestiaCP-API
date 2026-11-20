# Packages

```php
$packages = $client->getModulePackage();
```

The package module works on the server's hosting packages, so it takes no username.

## Listing

```php
$packages->list(); // ['default' => ['WEB_DOMAINS' => '...', 'DISK_QUOTA' => '...', ...], ...]

$packages->detail('default'); // the fields of one package
```

`list()` returns every package, keyed by name. `detail()` returns the fields of one package.

To move a user to another package, use `changePackage()` in the [user module](users.md#changing-the-password-contact-email-or-package).

## Adding

HestiaCP needs every setting of a package, so start from an existing one and change what differs:

```php
$packages->add('gold', [
	'WEB_DOMAINS' => 10,
	'DISK_QUOTA' => 10240, // in MB
	'BANDWIDTH' => 'unlimited'
] + $packages->detail('default'));

// replace an existing package
$packages->add('gold', ['WEB_DOMAINS' => 20] + $packages->detail('gold'), true);
```

The keys are the fields `detail()` returns. HestiaCP checks the values; a wrong one throws a `ProcessException`. A key that isn't upper case, or a value with a quote or a line break, throws an `InvalidArgumentException` before anything is sent.

`add()` sends two requests: `v-make-tmp-file` writes the settings to `/tmp` on the server, where `v-add-user-package` reads them.

## Copying, renaming and deleting

```php
$packages->copy('gold', 'gold-2027');
$packages->rename('gold-2027', 'platinum');

$packages->delete('platinum');
```

As of HestiaCP 1.10.9, `rename()` through the API renames the package but leaves its users on the old name, which no longer exists: the script's loop that moves them calls two other scripts without their path, and fails silently. Until that's fixed, rename a package that users have in three steps:

```php
$packages->copy('gold', 'platinum');
$users->changePackage('alice', 'platinum'); // each user that has gold
$packages->delete('gold');
```

Deleting a package moves the users that have it to the `default` package. As of HestiaCP 1.10.9, it also moves the users of every package whose name starts with the deleted one: deleting `gold` moves the users of `gold-plus` too.
