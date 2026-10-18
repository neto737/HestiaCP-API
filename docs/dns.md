# DNS

```php
$dns = $client->getModuleDns('admin');
```

The DNS module works on the DNS domains (zones) of one user, given when you get the module.

## Domains

```php
$dns->listDomains(); // ['example.com' => ['IP' => '...', 'TPL' => 'default', 'TTL' => '14400', 'RECORDS' => '...', ...], ...]
$dns->listDomain('example.com'); // the fields of one domain

$dns->addDomain('example.com', '192.0.2.10');

$dns->suspendDomain('example.com');
$dns->unsuspendDomain('example.com');

$dns->deleteDomain('example.com');
```

`addDomain()` creates the zone with the records of the user's DNS template, pointed at the IP address. It also takes these optional arguments:

```php
$dns->addDomain(
	'example.com',
	'192.0.2.10',
	['ns1.example.org', 'ns2.example.org'], // nameservers (default: the user's)
	true,                                   // restart the DNS server
	true                                    // turn on DNSSEC
);
```

Pass two to eight nameservers, or none. HestiaCP ignores a single nameserver and uses the user's instead, so `addDomain()` throws an `InvalidArgumentException` for one, and for more than eight.

## Changing a domain

```php
$dns->changeDomainIp('example.com', '192.0.2.20');         // in the domain and in every record that holds the old IP
$dns->changeDomainTtl('example.com', 3600);                // in seconds
$dns->changeDomainExpiration('example.com', '2027-10-18'); // YYYY-MM-DD
$dns->changeDomainSoa('example.com', 'ns1.example.org');   // primary nameserver in the SOA record
$dns->changeDomainTemplate('example.com', 'gmail');
```

`changeDomainTemplate()` replaces the domain's records with the records of the new template, so records you added yourself are lost.

## DNSSEC

```php
$dns->changeDomainDnssec('example.com', true);  // turn on
$dns->changeDomainDnssec('example.com', false); // turn off and delete the keys

$dns->listDnssecPublicKey('example.com'); // ['RECORD' => '...', 'KEYTAG' => '...', 'ALGORITHM' => '...', 'KEY' => '...', 'DS' => '...', ...]
```

HestiaCP supports DNSSEC only when `DNS_CLUSTER_SYSTEM` is `hestia-zone` and BIND is newer than 9.16.18. Otherwise `changeDomainDnssec()` throws a `ProcessException` with the code `ResponseCode::E_DISABLED`.

## Records

Records are identified by their numeric ID.

```php
$dns->listRecords('example.com'); // [1 => ['RECORD' => '@', 'TYPE' => 'A', 'VALUE' => '192.0.2.10', 'TTL' => '14400', ...], ...]

$dns->addRecord('example.com', 'www', 'A', '192.0.2.10');
$dns->addRecord('example.com', '@', 'MX', 'mail.example.com', 20); // with priority 20

$dns->suspendRecord('example.com', 7);
$dns->unsuspendRecord('example.com', 7);

$dns->deleteRecord('example.com', 7);
```

The record name is relative to the domain, with `@` for the domain itself. HestiaCP adds the trailing dot to the value of NS, CNAME, MX, PTR and SRV records.

`addRecord()` also takes these optional arguments, after the priority:

```php
$dns->addRecord(
	'example.com',
	'_dmarc',
	'TXT',
	'v=DMARC1; p=none',
	null,  // priority, only used by MX and SRV records (default: 10)
	30,    // record ID (default: the next free ID)
	true,  // restart the DNS server
	300    // TTL in seconds (default: the domain's TTL)
);
```

`addRecord()` returns `true`, not the ID, so call `listRecords()` to find it, or pass your own.

### Changing a record

```php
$dns->changeRecord('example.com', 7, 'www', 'A', '192.0.2.20');

$dns->changeRecordId('example.com', 7, 30); // record 7 becomes record 30
```

`changeRecord()` replaces the record, so pass its name, type and value even if they don't change. The optional arguments are the priority (default: the current one), `$restart` and the TTL. Without a TTL, the record goes back to the domain's TTL. If nothing changes, HestiaCP fails with `ResponseCode::E_EXISTS`.

## Templates

```php
$dns->listTemplates(); // ['default', 'gmail', 'office365', ...]
$dns->listTemplate('gmail'); // the records of a template, keyed by ID
```

## Restarting the DNS server

`addDomain()`, `deleteDomain()`, `changeDomainIp()`, `changeDomainTtl()`, `changeDomainSoa()`, `changeDomainTemplate()`, `addRecord()`, `changeRecord()`, `changeRecordId()` and `deleteRecord()` take an optional `$restart` argument:

- `null` (default) leaves it to HestiaCP, which restarts the DNS server right away, or later when the server is set up for scheduled restarts.
- `true` restarts the DNS server right away.
- `false` doesn't restart it.

```php
$dns->deleteRecord('example.com', 7, false);
```

All methods except the `list*()` methods return `true` on success and throw an exception on failure (see [Error handling](errors.md)).
