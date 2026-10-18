# Mail

```php
$mail = $client->getModuleMail('admin');
```

The mail module works on the mail domains of one user, given when you get the module.

## Domains

```php
$mail->listDomains(); // ['example.com' => [...], ...]
$mail->listDomain('example.com'); // the fields of one domain

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
$mail->listAccount('example.com', 'info'); // ['ALIAS' => '...', 'FWD' => '...', 'FWD_ONLY' => '...', 'AUTOREPLY' => '...', ...]

$mail->addAccount('example.com', 'info', 'pa$$word'); // creates info@example.com

$mail->changeAccountPassword('example.com', 'info', 'otherPa$$word');

$mail->suspendAccount('example.com', 'info');
$mail->unsuspendAccount('example.com', 'info');

$mail->deleteAccount('example.com', 'info');
```

Accounts are given by the part before the `@`.

## Aliases

An alias is another address on the same domain that delivers to the account. Like accounts, it's given by the part before the `@`.

```php
$mail->addAccountAlias('example.com', 'info', 'sales'); // sales@example.com delivers to info@example.com

$mail->deleteAccountAlias('example.com', 'info', 'sales');
```

`listAccount()` shows an account's aliases, comma-separated, in `ALIAS`.

## Forwarding

Forwards take the full address, one at a time:

```php
$mail->addAccountForward('example.com', 'info', 'bob@example.org');

$mail->deleteAccountForward('example.com', 'info', 'bob@example.org');
```

By default, forwarded mail is also kept in the account's mailbox. To only forward it:

```php
$mail->addAccountForwardOnly('example.com', 'info');    // forward without keeping a copy
$mail->deleteAccountForwardOnly('example.com', 'info'); // keep a copy again
```

`listAccount()` shows the forwards, comma-separated, in `FWD`, and whether the account is forward only in `FWD_ONLY`.

## Autoreply

```php
$mail->addAccountAutoreply('example.com', 'info', "I'm away until Monday.\nFor urgent matters, write to bob@example.org.");

$mail->listAccountAutoreply('example.com', 'info'); // ['MSG' => "I'm away until Monday.\n..."]

$mail->deleteAccountAutoreply('example.com', 'info');
```

Calling `addAccountAutoreply()` again replaces the message.

All methods except the `list*()` methods return `true` on success and throw an exception on failure (see [Error handling](errors.md)).
