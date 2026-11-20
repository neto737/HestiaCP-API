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

The other options are left to HestiaCP's defaults unless set. Named arguments keep the call readable:

```php
$mail->addDomain(
	'example.com',
	dkimSize: 4096,         // DKIM key size in bits (default 2048)
	dkimSelector: 's2026',  // DKIM selector (default "mail")
	rejectSpam: true,       // reject messages marked as spam instead of delivering them (default false)
	restart: false          // don't restart the web and proxy servers now
);
```

Without SpamAssassin on the server, HestiaCP turns antispam and spam rejection off whatever you pass; without ClamAV, antivirus.

To change them later:

```php
$mail->addDomainAntispam('example.com');
$mail->deleteDomainAntispam('example.com');

$mail->addDomainAntivirus('example.com');
$mail->deleteDomainAntivirus('example.com');

$mail->addDomainRejectSpam('example.com');    // reject messages marked as spam
$mail->deleteDomainRejectSpam('example.com'); // deliver them again
```

Turning one on that is already on, or off that is already off, throws a `ProcessException`: `E_EXISTS` or `E_NOTEXIST`. These methods only set HestiaCP's flags: without SpamAssassin or ClamAV, nothing filters the mail even when the flag is on.

## Catch-all address

Mail sent to an account that doesn't exist goes to the catch-all address, if the domain has one:

```php
$mail->addDomainCatchall('example.com', 'postmaster@example.org');
$mail->changeDomainCatchall('example.com', 'admin@example.org');
$mail->deleteDomainCatchall('example.com');
```

## DKIM

```php
$mail->listDomainDkim('example.com');    // the domain's DKIM key
$mail->listDomainDkimDns('example.com'); // the DNS record to publish

$mail->deleteDomainDkim('example.com');               // turn DKIM off and delete the key
$mail->addDomainDkim('example.com');                  // turn it on with a new key
$mail->addDomainDkim('example.com', 4096, 's2026');   // key size and selector (defaults: 2048, "mail")
```

## SSL certificates

The mail domain's certificate covers `mail.example.com`. Like the web module, it reads the files from a directory on the server, named after the domain: `example.com.crt`, `example.com.key` and, optionally, `example.com.ca`. See [Uploading a certificate](web.md#uploading-a-certificate) to send them through the API.

```php
$mail->listDomainSsl('example.com'); // ['CRT' => '...', 'KEY' => '...', 'CA' => '...', 'SUBJECT' => '...', ...]

$mail->addDomainSsl('example.com', '/tmp');
$mail->changeDomainSslCert('example.com', '/tmp'); // replace the certificate
$mail->deleteDomainSsl('example.com');
```

For a Let's Encrypt certificate, use `addDomainLetsEncrypt('example.com', mail: true)` in the [web module](web.md#lets-encrypt).

## Accounts

```php
$mail->listAccounts('example.com'); // ['info' => [...], ...]
$mail->listAccount('example.com', 'info'); // ['ALIAS' => '...', 'FWD' => '...', 'FWD_ONLY' => '...', 'AUTOREPLY' => '...', ...]

$mail->addAccount('example.com', 'info', 'pa$$word'); // creates info@example.com
$mail->addAccount('example.com', 'sales', 'pa$$word', 1024); // with a 1024 MB quota; 0 or no quota means unlimited

$mail->changeAccountPassword('example.com', 'info', 'otherPa$$word');
$mail->changeAccountQuota('example.com', 'info', 2048); // in MB, 0 for unlimited

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
