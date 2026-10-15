# Mail

```php
$mail = $client->getModuleMail('admin');
```

The mail module works on the mail domains of one user, given when you get the module.

## Domains

```php
$mail->listDomains(); // ['example.com' => [...], ...]

$mail->addDomain('example.com');

$mail->suspendDomain('example.com');
$mail->unsuspendDomain('example.com');

$mail->deleteDomain('example.com');
```

`addDomain()` turns on antispam, antivirus and DKIM by default. Pass `false` to turn any of them off:

```php
$mail->addDomain('example.com', true, true, false); // antispam, antivirus, no DKIM
```

## DKIM

```php
$mail->listDomainDkim('example.com');    // the domain's DKIM key
$mail->listDomainDkimDns('example.com'); // the DNS record to publish
```

## Accounts

```php
$mail->listAccounts('example.com'); // ['info' => [...], ...]

$mail->addAccount('example.com', 'info', 'pa$$word'); // creates info@example.com

$mail->changeAccountPassword('example.com', 'info', 'otherPa$$word');

$mail->suspendAccount('example.com', 'info');
$mail->unsuspendAccount('example.com', 'info');

$mail->deleteAccount('example.com', 'info');
```

Accounts are given by the part before the `@`.

All methods except the `list*()` methods return `true` on success and throw an exception on failure (see [Error handling](errors.md)).
