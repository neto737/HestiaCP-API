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
