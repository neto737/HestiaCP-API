<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Module;

use neto737\HestiaCP\Client;
use neto737\HestiaCP\Command\Dns\AddDnsDomain;
use neto737\HestiaCP\Command\Dns\AddDnsRecord;
use neto737\HestiaCP\Command\Dns\ChangeDnsDomainDnssec;
use neto737\HestiaCP\Command\Dns\ChangeDnsDomainExp;
use neto737\HestiaCP\Command\Dns\ChangeDnsDomainIp;
use neto737\HestiaCP\Command\Dns\ChangeDnsDomainSoa;
use neto737\HestiaCP\Command\Dns\ChangeDnsDomainTpl;
use neto737\HestiaCP\Command\Dns\ChangeDnsDomainTtl;
use neto737\HestiaCP\Command\Dns\ChangeDnsRecord;
use neto737\HestiaCP\Command\Dns\ChangeDnsRecordId;
use neto737\HestiaCP\Command\Dns\DeleteDnsDomain;
use neto737\HestiaCP\Command\Dns\DeleteDnsRecord;
use neto737\HestiaCP\Command\Dns\ListDnsDomain;
use neto737\HestiaCP\Command\Dns\ListDnsDomains;
use neto737\HestiaCP\Command\Dns\ListDnsRecords;
use neto737\HestiaCP\Command\Dns\ListDnssecPublicKey;
use neto737\HestiaCP\Command\Dns\ListDnsTemplate;
use neto737\HestiaCP\Command\Dns\ListDnsTemplates;
use neto737\HestiaCP\Command\Dns\SuspendDnsDomain;
use neto737\HestiaCP\Command\Dns\SuspendDnsRecord;
use neto737\HestiaCP\Command\Dns\UnsuspendDnsDomain;
use neto737\HestiaCP\Command\Dns\UnsuspendDnsRecord;

class Dns extends Module {

	private string $user;

	public function __construct(Client $client, string $user) {
		parent::__construct($client);
		$this->user = $user;
	}

	/**
	 * This function obtains the list of all user's DNS domains.
	 *
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listDomains(): array {
		return $this->client->send(new ListDnsDomains($this->user));
	}

	/**
	 * This function obtains the parameters of a DNS domain.
	 *
	 * @param string $domain
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listDomain(string $domain): array {
		return $this->client->send(new ListDnsDomain($this->user, $domain));
	}

	/**
	 * This function adds a DNS domain, with the records of the user's DNS template.
	 *
	 * @param string       $domain
	 * @param string       $ip
	 * @param list<string> $nameservers Two to eight nameservers, or none for the user's
	 * @param bool|null    $restart     Restart the DNS server, or null for HestiaCP's default
	 * @param bool|null    $dnssec      Turn on DNSSEC, or null for HestiaCP's default (off)
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\InvalidArgumentException
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addDomain(string $domain, string $ip, array $nameservers = [], ?bool $restart = null, ?bool $dnssec = null): bool {
		return $this->client->send(new AddDnsDomain($this->user, $domain, $ip, $nameservers, $restart, $dnssec));
	}

	/**
	 * This function deletes a DNS domain and all its records.
	 *
	 * @param string    $domain
	 * @param bool|null $restart Restart the DNS server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteDomain(string $domain, ?bool $restart = null): bool {
		return $this->client->send(new DeleteDnsDomain($this->user, $domain, $restart));
	}

	/**
	 * This function suspends a DNS domain.
	 *
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function suspendDomain(string $domain): bool {
		return $this->client->send(new SuspendDnsDomain($this->user, $domain));
	}

	/**
	 * This function unsuspends a DNS domain.
	 *
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function unsuspendDomain(string $domain): bool {
		return $this->client->send(new UnsuspendDnsDomain($this->user, $domain));
	}

	/**
	 * This function changes the IP address of a DNS domain, in the domain and in every record that holds it.
	 *
	 * @param string    $domain
	 * @param string    $ip
	 * @param bool|null $restart Restart the DNS server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeDomainIp(string $domain, string $ip, ?bool $restart = null): bool {
		return $this->client->send(new ChangeDnsDomainIp($this->user, $domain, $ip, $restart));
	}

	/**
	 * This function changes the TTL of a DNS domain.
	 *
	 * @param string    $domain
	 * @param int       $ttl     TTL in seconds
	 * @param bool|null $restart Restart the DNS server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeDomainTtl(string $domain, int $ttl, ?bool $restart = null): bool {
		return $this->client->send(new ChangeDnsDomainTtl($this->user, $domain, $ttl, $restart));
	}

	/**
	 * This function changes the expiration date of a DNS domain.
	 *
	 * @param string $domain
	 * @param string $date   YYYY-MM-DD
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeDomainExpiration(string $domain, string $date): bool {
		return $this->client->send(new ChangeDnsDomainExp($this->user, $domain, $date));
	}

	/**
	 * This function changes the primary nameserver in the SOA record of a DNS domain.
	 *
	 * @param string    $domain
	 * @param string    $soa
	 * @param bool|null $restart Restart the DNS server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeDomainSoa(string $domain, string $soa, ?bool $restart = null): bool {
		return $this->client->send(new ChangeDnsDomainSoa($this->user, $domain, $soa, $restart));
	}

	/**
	 * This function changes the template of a DNS domain and replaces its records with the template's.
	 *
	 * @param string    $domain
	 * @param string    $template
	 * @param bool|null $restart  Restart the DNS server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeDomainTemplate(string $domain, string $template, ?bool $restart = null): bool {
		return $this->client->send(new ChangeDnsDomainTpl($this->user, $domain, $template, $restart));
	}

	/**
	 * This function turns DNSSEC on or off for a DNS domain.
	 *
	 * @param string $domain
	 * @param bool   $enabled
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeDomainDnssec(string $domain, bool $enabled): bool {
		return $this->client->send(new ChangeDnsDomainDnssec($this->user, $domain, $enabled));
	}

	/**
	 * This function obtains the DNSSEC public key and DS record of a DNS domain.
	 *
	 * @param string $domain
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listDnssecPublicKey(string $domain): array {
		return $this->client->send(new ListDnssecPublicKey($this->user, $domain));
	}

	/**
	 * This function obtains the records of a DNS domain, keyed by record ID.
	 *
	 * @param string $domain
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listRecords(string $domain): array {
		return $this->client->send(new ListDnsRecords($this->user, $domain));
	}

	/**
	 * This function adds a DNS record.
	 *
	 * @param string    $domain
	 * @param string    $record   Record name, e.g. www, or @ for the domain itself
	 * @param string    $type     Record type, e.g. A, AAAA, CNAME, MX, TXT, SRV, CAA
	 * @param string    $value
	 * @param int|null  $priority Priority of an MX or SRV record, or null for HestiaCP's default (10)
	 * @param int|null  $id       Record ID, or null for the next free ID
	 * @param bool|null $restart  Restart the DNS server, or null for HestiaCP's default
	 * @param int|null  $ttl      TTL in seconds, or null for the domain's TTL
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addRecord(string $domain, string $record, string $type, string $value, ?int $priority = null, ?int $id = null, ?bool $restart = null, ?int $ttl = null): bool {
		return $this->client->send(new AddDnsRecord($this->user, $domain, $record, $type, $value, $priority, $id, $restart, $ttl));
	}

	/**
	 * This function replaces a DNS record.
	 *
	 * @param string    $domain
	 * @param int       $id
	 * @param string    $record   Record name, e.g. www, or @ for the domain itself
	 * @param string    $type     Record type, e.g. A, AAAA, CNAME, MX, TXT, SRV, CAA
	 * @param string    $value
	 * @param int|null  $priority Priority of an MX or SRV record, or null to keep the current one
	 * @param bool|null $restart  Restart the DNS server, or null for HestiaCP's default
	 * @param int|null  $ttl      TTL in seconds, or null for the domain's TTL
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeRecord(string $domain, int $id, string $record, string $type, string $value, ?int $priority = null, ?bool $restart = null, ?int $ttl = null): bool {
		return $this->client->send(new ChangeDnsRecord($this->user, $domain, $id, $record, $type, $value, $priority, $restart, $ttl));
	}

	/**
	 * This function changes the ID of a DNS record.
	 *
	 * @param string    $domain
	 * @param int       $id
	 * @param int       $newId
	 * @param bool|null $restart Restart the DNS server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeRecordId(string $domain, int $id, int $newId, ?bool $restart = null): bool {
		return $this->client->send(new ChangeDnsRecordId($this->user, $domain, $id, $newId, $restart));
	}

	/**
	 * This function suspends a DNS record.
	 *
	 * @param string $domain
	 * @param int    $id
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function suspendRecord(string $domain, int $id): bool {
		return $this->client->send(new SuspendDnsRecord($this->user, $domain, $id));
	}

	/**
	 * This function unsuspends a DNS record.
	 *
	 * @param string $domain
	 * @param int    $id
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function unsuspendRecord(string $domain, int $id): bool {
		return $this->client->send(new UnsuspendDnsRecord($this->user, $domain, $id));
	}

	/**
	 * This function deletes a DNS record.
	 *
	 * @param string    $domain
	 * @param int       $id
	 * @param bool|null $restart Restart the DNS server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteRecord(string $domain, int $id, ?bool $restart = null): bool {
		return $this->client->send(new DeleteDnsRecord($this->user, $domain, $id, $restart));
	}

	/**
	 * This function obtains the names of the DNS templates.
	 *
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listTemplates(): array {
		return $this->client->send(new ListDnsTemplates());
	}

	/**
	 * This function obtains the records of a DNS template, keyed by record ID.
	 *
	 * @param string $template
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listTemplate(string $template): array {
		return $this->client->send(new ListDnsTemplate($template));
	}
}
