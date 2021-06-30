<?php

namespace neto737\HestiaCP\Module;

use Nette\Utils\ArrayHash;
use neto737\HestiaCP\Client;
use neto737\HestiaCP\Command\Add\MailAccount;
use neto737\HestiaCP\Command\Add\MailDomain;
use neto737\HestiaCP\Command\Change\MailAccountPassword;
use neto737\HestiaCP\Command\Suspend\MailAccount as SuspendMailAccount;
use neto737\HestiaCP\Command\Suspend\MailDomain as SuspendMailDomain;
use neto737\HestiaCP\Command\Unsuspend\MailAccount as UnsuspendMailAccount;
use neto737\HestiaCP\Command\Unsuspend\MailDomain as UnsuspendMailDomain;
use neto737\HestiaCP\Command\Delete\MailAccount as DeleteMailAccount;
use neto737\HestiaCP\Command\Delete\MailDomain as DeleteMailDomain;
use neto737\HestiaCP\Command\Lists\MailAccounts;
use neto737\HestiaCP\Command\Lists\MailDomainDkim;
use neto737\HestiaCP\Command\Lists\MailDomainDkimDns;
use neto737\HestiaCP\Command\Lists\MailDomains;

class Mails extends Module {

	/** @var string */
	private $user;

	public function __construct(Client $client, string $user) {
		parent::__construct($client);
		$this->user = $user;
	}

	/**
	 * @param string $domain
	 * @return ArrayHash[]
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function listAccounts(string $domain): array {
		return $this->client->send(new MailAccounts($this->user, $domain));
	}

	/**
	 * @param string $domain
	 * @param string $account
	 * @param string $password
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function addAccount(string $domain, string $account, string $password): bool {
		return $this->client->send(new MailAccount($this->user, $domain, $account, $password));
	}

	/**
	 * @param string $domain
	 * @param string $account
	 * @param string $password
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function changeAccountPassword(string $domain, string $account, string $password): bool {
		return $this->client->send(new MailAccountPassword($this->user, $domain, $account, $password));
	}

	/**
	 * @param string $domain
	 * @param string $account
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function suspendAccount(string $domain, string $account): bool {
		return $this->client->send(new SuspendMailAccount($this->user,  $domain, $account));
	}

	/**
	 * @param string $domain
	 * @param string $account
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function unsuspendAccount(string $domain, string $account): bool {
		return $this->client->send(new UnsuspendMailAccount($this->user,  $domain, $account));
	}

	/**
	 * @param string $domain
	 * @param string $account
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function deleteAccount(string $domain, string $account): bool {
		return $this->client->send(new DeleteMailAccount($this->user, $domain, $account));
	}

	/**
	 * @param string $domain
	 * @param bool   $antispam
	 * @param bool   $antivirus
	 * @param bool   $dkim
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function addDomain(string $domain, bool $antispam = true, bool $antivirus = true, bool $dkim = true): bool {
		return $this->client->send(new MailDomain($this->user, $domain, $antispam, $antivirus, $dkim));
	}

	/**
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function suspendDomain(string $domain): bool {
		return $this->client->send(new SuspendMailDomain($this->user, $domain));
	}

	/**
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function unsuspendDomain(string $domain): bool {
		return $this->client->send(new UnsuspendMailDomain($this->user, $domain));
	}

	/**
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function deleteDomain(string $domain): bool {
		return $this->client->send(new DeleteMailDomain($this->user, $domain));
	}

	/**
	 * @param string $domain
	 * @return ArrayHash[]
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function listDomainDkim(string $domain): array {
		return $this->client->send(new MailDomainDkim($this->user, $domain));
	}

	/**
	 * @param string $domain
	 * @return ArrayHash[]
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function listDomainDkimDns(string $domain): array {
		return $this->client->send(new MailDomainDkimDns($this->user, $domain));
	}

	/**
	 * @return ArrayHash[]
	 * @throws \neto737\HestiaCP\ClientException
	 * @throws \neto737\HestiaCP\ProcessException
	 */
	public function listDomains(): array {
		return $this->client->send(new MailDomains($this->user));
	}
}
