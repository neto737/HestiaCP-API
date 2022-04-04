<?php

namespace heliocg\HestiaCP\Module;

use Nette\Utils\ArrayHash;
use heliocg\HestiaCP\Client;
use heliocg\HestiaCP\Command\Add\LetsEncryptDomain;
use heliocg\HestiaCP\Command\Add\WebDomain;
use heliocg\HestiaCP\Command\Add\WebDomainFtp;
use heliocg\HestiaCP\Command\Change\WebDomainFtpPassword;
use heliocg\HestiaCP\Command\Change\WebDomainFtpPath;
use heliocg\HestiaCP\Command\Suspend\WebDomain as SuspendWebDomain;
use heliocg\HestiaCP\Command\Unsuspend\WebDomain as UnsuspendWebDomain;
use heliocg\HestiaCP\Command\Delete\LetsEncryptDomain as DeleteLetsEncryptDomain;
use heliocg\HestiaCP\Command\Delete\WebDomain as DeleteWebDomain;
use heliocg\HestiaCP\Command\Delete\WebDomainFtp as DeleteWebDomainFtp;
use heliocg\HestiaCP\Command\Lists\WebDomains;

class Webs extends Module {

	/** @var string */
	private $user;

	public function __construct(Client $client, string $user) {
		parent::__construct($client);
		$this->user = $user;
	}

	/**
	 * The function adds virtual host to a server.
	 * 
	 * @param string      $domain
	 * @param string|null $ip
	 * @param string|null $aliases
	 * @param string|null $proxyExtensions
	 * @param bool        $restart
	 * @return bool
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function addDomain(string $domain, string $ip = null, string $aliases = null, string $proxyExtensions = null, bool $restart = false): bool {
		return $this->client->send(new WebDomain($this->user, $domain, $ip, $aliases, $proxyExtensions, $restart));
	}

	/**
	 * The function for suspending the site's operation. 
	 * 
	 * @param string      $domain
	 * @param bool        $restart
	 * @return bool
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function suspendDomain(string $domain, bool $restart = false): bool {
		return $this->client->send(new SuspendWebDomain($this->user, $domain, $restart));
	}

	/**
	 * The function of unsuspending the domain.
	 * 
	 * @param string      $domain
	 * @param bool        $restart
	 * @return bool
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function unsuspendDomain(string $domain, bool $restart = false): bool {
		return $this->client->send(new UnsuspendWebDomain($this->user, $domain, $restart));
	}

	/**
	 * The call of function leads to the removal of domain and all its components (statistics, folders contents, ssl certificates, etc.).
	 * 
	 * @param string $domain
	 * @return bool
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function deleteDomain(string $domain): bool {
		return $this->client->send(new DeleteWebDomain($this->user, $domain));
	}

	/**
	 * The function check and validates domain with Let's Encrypt
	 * 
	 * @param string      $domain
	 * @param string|null $aliases
	 * @param bool        $restart
	 * @return bool
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function addDomainLetsEncrypt(string $domain, string $aliases = null, bool $restart = false): bool {
		return $this->client->send(new LetsEncryptDomain($this->user, $domain, $aliases, $restart));
	}

	/**
	 * The function turns off letsencrypt SSL support for a domain.
	 * 
	 * @param string $domain
	 * @param bool   $restart
	 * @return bool
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function deleteDomainLetsEncrypt(string $domain, bool $restart = false): bool {
		return $this->client->send(new DeleteLetsEncryptDomain($this->user, $domain, $restart));
	}

	/**
	 * The function creates additional ftp account for web domain.
	 * 
	 * @param string      $domain
	 * @param string      $ftpUser
	 * @param string      $ftpPassword
	 * @param string|null $ftpPath
	 * @return bool
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function addDomainFtp(string $domain, string $ftpUser, string $ftpPassword, string $ftpPath = null): bool {
		return $this->client->send(new WebDomainFtp($this->user, $domain, $ftpUser, $ftpPassword, $ftpPath));
	}

	/**
	 * The function changes ftp user password.
	 * 
	 * @param string      $domain
	 * @param string      $ftpUser
	 * @param string      $ftpPassword
	 * @return bool
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function changeDomainFtpPassword(string $domain, string $ftpUser, string $ftpPassword): bool {
		return $this->client->send(new WebDomainFtpPassword($this->user, $domain, $ftpUser, $ftpPassword));
	}

	/**
	 * The function changes ftp user path.
	 * 
	 * @param string $domain
	 * @param string $ftpUser
	 * @param string $ftpPath
	 * @return bool
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function changeDomainFtpPath(string $domain, string $ftpUser, string $ftpPath): bool {
		return $this->client->send(new WebDomainFtpPath($this->user, $domain, $ftpUser, $ftpPath));
	}

	/**
	 * The function deletes additional ftp account.
	 * 
	 * @param string $domain
	 * @param string $ftpUser
	 * @return bool
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function deleteDomainFtp(string $domain, string $ftpUser): bool {
		return $this->client->send(new DeleteWebDomainFtp($this->user, $domain, $ftpUser));
	}

	/**
	 * The function to obtain the list of all user web domains.
	 * 
	 * @return ArrayHash[]
	 * @throws \heliocg\HestiaCP\ClientException
	 * @throws \heliocg\HestiaCP\ProcessException
	 */
	public function listDomains(): array {
		return $this->client->send(new WebDomains($this->user));
	}
}
