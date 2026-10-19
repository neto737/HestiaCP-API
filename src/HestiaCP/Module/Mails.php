<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Module;

use neto737\HestiaCP\Client;
use neto737\HestiaCP\Command\Mail\AddMailAccount;
use neto737\HestiaCP\Command\Mail\AddMailAccountAlias;
use neto737\HestiaCP\Command\Mail\AddMailAccountAutoreply;
use neto737\HestiaCP\Command\Mail\AddMailAccountForward;
use neto737\HestiaCP\Command\Mail\AddMailAccountFwdOnly;
use neto737\HestiaCP\Command\Mail\AddMailDomain;
use neto737\HestiaCP\Command\Mail\ChangeMailAccountPassword;
use neto737\HestiaCP\Command\Mail\SuspendMailAccount;
use neto737\HestiaCP\Command\Mail\SuspendMailDomain;
use neto737\HestiaCP\Command\Mail\UnsuspendMailAccount;
use neto737\HestiaCP\Command\Mail\UnsuspendMailDomain;
use neto737\HestiaCP\Command\Mail\DeleteMailAccount;
use neto737\HestiaCP\Command\Mail\DeleteMailAccountAlias;
use neto737\HestiaCP\Command\Mail\DeleteMailAccountAutoreply;
use neto737\HestiaCP\Command\Mail\DeleteMailAccountForward;
use neto737\HestiaCP\Command\Mail\DeleteMailAccountFwdOnly;
use neto737\HestiaCP\Command\Mail\DeleteMailDomain;
use neto737\HestiaCP\Command\Mail\ListMailAccount;
use neto737\HestiaCP\Command\Mail\ListMailAccountAutoreply;
use neto737\HestiaCP\Command\Mail\ListMailAccounts;
use neto737\HestiaCP\Command\Mail\ListMailDomain;
use neto737\HestiaCP\Command\Mail\ListMailDomainDkim;
use neto737\HestiaCP\Command\Mail\ListMailDomainDkimDns;
use neto737\HestiaCP\Command\Mail\ListMailDomains;

class Mails extends Module {

	private string $user;

	public function __construct(Client $client, string $user) {
		parent::__construct($client);
		$this->user = $user;
	}

	/**
	 * This function of obtaining the list of all user domains.
	 *
	 * @param string $domain
	 * @return array<array<mixed>>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listAccounts(string $domain): array {
		return $this->client->send(new ListMailAccounts($this->user, $domain));
	}

	/**
	 * This function add new email account.
	 *
	 * @param string $domain
	 * @param string $account
	 * @param string $password
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addAccount(string $domain, string $account, #[\SensitiveParameter] string $password): bool {
		return $this->client->send(new AddMailAccount($this->user, $domain, $account, $password));
	}

	/**
	 * This function changes email account password.
	 *
	 * @param string $domain
	 * @param string $account
	 * @param string $password
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeAccountPassword(string $domain, string $account, #[\SensitiveParameter] string $password): bool {
		return $this->client->send(new ChangeMailAccountPassword($this->user, $domain, $account, $password));
	}

	/**
	 * This function suspends mail account.
	 *
	 * @param string $domain
	 * @param string $account
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function suspendAccount(string $domain, string $account): bool {
		return $this->client->send(new SuspendMailAccount($this->user, $domain, $account));
	}

	/**
	 * This function unsuspends mail account.
	 *
	 * @param string $domain
	 * @param string $account
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function unsuspendAccount(string $domain, string $account): bool {
		return $this->client->send(new UnsuspendMailAccount($this->user, $domain, $account));
	}

	/**
	 * This function deletes email account.
	 *
	 * @param string $domain
	 * @param string $account
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteAccount(string $domain, string $account): bool {
		return $this->client->send(new DeleteMailAccount($this->user, $domain, $account));
	}

	/**
	 * This function adds MAIL domain.
	 *
	 * @param string $domain
	 * @param bool   $antispam
	 * @param bool   $antivirus
	 * @param bool   $dkim
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addDomain(string $domain, bool $antispam = true, bool $antivirus = true, bool $dkim = true): bool {
		return $this->client->send(new AddMailDomain($this->user, $domain, $antispam, $antivirus, $dkim));
	}

	/**
	 * This function suspends mail domain.
	 *
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function suspendDomain(string $domain): bool {
		return $this->client->send(new SuspendMailDomain($this->user, $domain));
	}

	/**
	 * This function unsuspends mail domain.
	 *
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function unsuspendDomain(string $domain): bool {
		return $this->client->send(new UnsuspendMailDomain($this->user, $domain));
	}

	/**
	 * This function for deleting MAIL domain. By deleting it all accounts will also be deleted.
	 *
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteDomain(string $domain): bool {
		return $this->client->send(new DeleteMailDomain($this->user, $domain));
	}

	/**
	 * This function of obtaining domain dkim files.
	 *
	 * @param string $domain
	 * @return array<array<mixed>>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listDomainDkim(string $domain): array {
		return $this->client->send(new ListMailDomainDkim($this->user, $domain));
	}

	/**
	 * This function of obtaining domain dkim dns records for proper setup.
	 *
	 * @param string $domain
	 * @return array<array<mixed>>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listDomainDkimDns(string $domain): array {
		return $this->client->send(new ListMailDomainDkimDns($this->user, $domain));
	}

	/**
	 * This function of obtaining the list of all user domains.
	 *
	 * @return array<array<mixed>>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listDomains(): array {
		return $this->client->send(new ListMailDomains($this->user));
	}

	/**
	 * This function obtains the parameters of a mail domain.
	 *
	 * @param string $domain
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listDomain(string $domain): array {
		return $this->client->send(new ListMailDomain($this->user, $domain));
	}

	/**
	 * This function obtains the parameters of a mail account, including its aliases and forwards.
	 *
	 * @param string $domain
	 * @param string $account
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listAccount(string $domain, string $account): array {
		return $this->client->send(new ListMailAccount($this->user, $domain, $account));
	}

	/**
	 * This function adds an alias to a mail account.
	 *
	 * @param string $domain
	 * @param string $account
	 * @param string $alias   The part before the @, on the account's domain
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addAccountAlias(string $domain, string $account, string $alias): bool {
		return $this->client->send(new AddMailAccountAlias($this->user, $domain, $account, $alias));
	}

	/**
	 * This function deletes an alias from a mail account.
	 *
	 * @param string $domain
	 * @param string $account
	 * @param string $alias   The part before the @
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteAccountAlias(string $domain, string $account, string $alias): bool {
		return $this->client->send(new DeleteMailAccountAlias($this->user, $domain, $account, $alias));
	}

	/**
	 * This function adds a forwarding address to a mail account.
	 *
	 * @param string $domain
	 * @param string $account
	 * @param string $forward Full email address
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addAccountForward(string $domain, string $account, string $forward): bool {
		return $this->client->send(new AddMailAccountForward($this->user, $domain, $account, $forward));
	}

	/**
	 * This function deletes a forwarding address from a mail account.
	 *
	 * @param string $domain
	 * @param string $account
	 * @param string $forward Full email address
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteAccountForward(string $domain, string $account, string $forward): bool {
		return $this->client->send(new DeleteMailAccountForward($this->user, $domain, $account, $forward));
	}

	/**
	 * This function makes a mail account forward only: mail is forwarded and not kept in the mailbox.
	 *
	 * @param string $domain
	 * @param string $account
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addAccountForwardOnly(string $domain, string $account): bool {
		return $this->client->send(new AddMailAccountFwdOnly($this->user, $domain, $account));
	}

	/**
	 * This function makes a mail account keep forwarded mail in the mailbox again.
	 *
	 * @param string $domain
	 * @param string $account
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteAccountForwardOnly(string $domain, string $account): bool {
		return $this->client->send(new DeleteMailAccountFwdOnly($this->user, $domain, $account));
	}

	/**
	 * This function obtains the autoreply message of a mail account.
	 *
	 * @param string $domain
	 * @param string $account
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listAccountAutoreply(string $domain, string $account): array {
		return $this->client->send(new ListMailAccountAutoreply($this->user, $domain, $account));
	}

	/**
	 * This function turns on the autoreply of a mail account, or replaces its message.
	 *
	 * @param string $domain
	 * @param string $account
	 * @param string $message
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addAccountAutoreply(string $domain, string $account, string $message): bool {
		return $this->client->send(new AddMailAccountAutoreply($this->user, $domain, $account, $message));
	}

	/**
	 * This function turns off the autoreply of a mail account.
	 *
	 * @param string $domain
	 * @param string $account
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteAccountAutoreply(string $domain, string $account): bool {
		return $this->client->send(new DeleteMailAccountAutoreply($this->user, $domain, $account));
	}
}
