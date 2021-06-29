<?php

namespace neto737\HestiaCP\Module;

use Nette\Utils\ArrayHash;
use neto737\HestiaCP\Client;
use neto737\HestiaCP\Command\Add\LetsEncryptDomain;
use neto737\HestiaCP\Command\Add\WebDomain;
use neto737\HestiaCP\Command\Add\WebDomainFtp;
use neto737\HestiaCP\Command\Change\WebDomainFtpPassword;
use neto737\HestiaCP\Command\Change\WebDomainFtpPath;
use neto737\HestiaCP\Command\Delete\LetsEncryptDomain as DeleteLetsEncryptDomain;
use neto737\HestiaCP\Command\Delete\WebDomain as DeleteWebDomain;
use neto737\HestiaCP\Command\Delete\WebDomainFtp as DeleteWebDomainFtpAlias;
use neto737\HestiaCP\Command\Lists\WebDomains;

class Webs extends Module {

	/** @var string */
	private $user;

	public function __construct(Client $client, string $user) {
		parent::__construct($client);
		$this->user = $user;
	}

	/**
	 * @param string      $domain
	 * @param string|null $ip
	 * @param string|null $aliases
	 * @param string|null $proxyExtensions
	 * @param bool        $restart
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function addDomain(string $domain, string $ip = null, string $aliases = null, string $proxyExtensions = null, bool $restart = false): bool {
		return $this->client->send(new WebDomain($this->user, $domain, $ip, $aliases, $proxyExtensions, $restart));
	}

	/**
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function deleteDomain(string $domain): bool {
		return $this->client->send(new DeleteWebDomain($this->user, $domain));
	}

	/**
	 * @param string      $domain
	 * @param string|null $aliases
	 * @param bool        $restart
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function addDomainLetsEncrypt(string $domain, string $aliases = null, bool $restart = false): bool {
		return $this->client->send(new LetsEncryptDomain($this->user, $domain, $aliases, $restart));
	}

	/**
	 * @param string $domain
	 * @param bool   $restart
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function deleteDomainLetsEncrypt(string $domain, bool $restart = false): bool {
		return $this->client->send(new DeleteLetsEncryptDomain($this->user, $domain, $restart));
	}

	/**
	 * @param string      $domain
	 * @param string      $ftpUser
	 * @param string      $ftpPassword
	 * @param string|null $ftpPath
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function addDomainFtp(string $domain, string $ftpUser, string $ftpPassword, string $ftpPath = null): bool {
		return $this->client->send(new WebDomainFtp($this->user, $domain, $ftpUser, $ftpPassword, $ftpPath));
	}

	/**
	 * @param string      $domain
	 * @param string      $ftpUser
	 * @param string      $ftpPassword
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function changeDomainFtpPassword(string $domain, string $ftpUser, string $ftpPassword): bool {
		return $this->client->send(new WebDomainFtpPassword($this->user, $domain, $ftpUser, $ftpPassword));
	}

	/**
	 * @param string $domain
	 * @param string $ftpUser
	 * @param string $ftpPath
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function changeDomainFtpPath(string $domain, string $ftpUser, string $ftpPath): bool {
		return $this->client->send(new WebDomainFtpPath($this->user, $domain, $ftpUser, $ftpPath));
	}

	/**
	 * @param string $domain
	 * @param string $ftpUser
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function deleteDomainFtp(string $domain, string $ftpUser): bool {
		return $this->client->send(new DeleteWebDomainFtpAlias($this->user, $domain, $ftpUser));
	}

	/**
	 * @return ArrayHash[]
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function listDomains(): array {
		return $this->client->send(new WebDomains($this->user));
	}
}
