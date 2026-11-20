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
use neto737\HestiaCP\Command\Mail\ChangeMailAccountQuota;
use neto737\HestiaCP\Command\Mail\AddMailDomainCatchall;
use neto737\HestiaCP\Command\Mail\ChangeMailDomainCatchall;
use neto737\HestiaCP\Command\Mail\DeleteMailDomainCatchall;
use neto737\HestiaCP\Command\Mail\AddMailDomainAntispam;
use neto737\HestiaCP\Command\Mail\DeleteMailDomainAntispam;
use neto737\HestiaCP\Command\Mail\AddMailDomainAntivirus;
use neto737\HestiaCP\Command\Mail\DeleteMailDomainAntivirus;
use neto737\HestiaCP\Command\Mail\AddMailDomainDkim;
use neto737\HestiaCP\Command\Mail\DeleteMailDomainDkim;
use neto737\HestiaCP\Command\Mail\AddMailDomainReject;
use neto737\HestiaCP\Command\Mail\DeleteMailDomainReject;
use neto737\HestiaCP\Command\Mail\ListMailDomainSsl;
use neto737\HestiaCP\Command\Mail\AddMailDomainSsl;
use neto737\HestiaCP\Command\Mail\ChangeMailDomainSslCert;
use neto737\HestiaCP\Command\Mail\DeleteMailDomainSsl;

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
	 * @param int|null $quota Mailbox size limit in MB, 0 for unlimited. HestiaCP's default (unlimited) when not set.
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addAccount(string $domain, string $account, #[\SensitiveParameter] string $password, ?int $quota = null): bool {
		return $this->client->send(new AddMailAccount($this->user, $domain, $account, $password, $quota));
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
	 * @param int|null    $dkimSize     DKIM key size in bits (HestiaCP's default: 2048)
	 * @param bool|null   $restart      Restart the web and proxy servers (HestiaCP's default: right away, or queued)
	 * @param bool|null   $rejectSpam   Reject messages marked as spam instead of delivering them (HestiaCP's default: no)
	 * @param string|null $dkimSelector DKIM selector (HestiaCP's default: mail)
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addDomain(string $domain, bool $antispam = true, bool $antivirus = true, bool $dkim = true, ?int $dkimSize = null, ?bool $restart = null, ?bool $rejectSpam = null, ?string $dkimSelector = null): bool {
		return $this->client->send(new AddMailDomain($this->user, $domain, $antispam, $antivirus, $dkim, $dkimSize, $restart, $rejectSpam, $dkimSelector));
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

	/**
	 * This function changes the mailbox size limit of a mail account.
	 *
	 * @param string $domain
	 * @param string $account
	 * @param int    $quota   Mailbox size limit in MB, 0 for unlimited
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\InvalidArgumentException
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeAccountQuota(string $domain, string $account, int $quota): bool {
		return $this->client->send(new ChangeMailAccountQuota($this->user, $domain, $account, $quota));
	}

	/**
	 * This function sets the address that receives mail sent to unknown accounts of a mail domain.
	 *
	 * @param string $domain
	 * @param string $email
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addDomainCatchall(string $domain, string $email): bool {
		return $this->client->send(new AddMailDomainCatchall($this->user, $domain, $email));
	}

	/**
	 * This function changes the catch-all address of a mail domain.
	 *
	 * @param string $domain
	 * @param string $email
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeDomainCatchall(string $domain, string $email): bool {
		return $this->client->send(new ChangeMailDomainCatchall($this->user, $domain, $email));
	}

	/**
	 * This function removes the catch-all address of a mail domain.
	 *
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteDomainCatchall(string $domain): bool {
		return $this->client->send(new DeleteMailDomainCatchall($this->user, $domain));
	}

	/**
	 * This function turns on spam filtering for a mail domain.
	 *
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addDomainAntispam(string $domain): bool {
		return $this->client->send(new AddMailDomainAntispam($this->user, $domain));
	}

	/**
	 * This function turns off spam filtering for a mail domain.
	 *
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteDomainAntispam(string $domain): bool {
		return $this->client->send(new DeleteMailDomainAntispam($this->user, $domain));
	}

	/**
	 * This function turns on virus scanning for a mail domain.
	 *
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addDomainAntivirus(string $domain): bool {
		return $this->client->send(new AddMailDomainAntivirus($this->user, $domain));
	}

	/**
	 * This function turns off virus scanning for a mail domain.
	 *
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteDomainAntivirus(string $domain): bool {
		return $this->client->send(new DeleteMailDomainAntivirus($this->user, $domain));
	}

	/**
	 * This function turns on DKIM signing for a mail domain and creates its key.
	 *
	 * @param string      $domain
	 * @param int|null    $dkimSize     DKIM key size in bits (HestiaCP's default: 2048)
	 * @param string|null $dkimSelector DKIM selector (HestiaCP's default: mail)
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addDomainDkim(string $domain, ?int $dkimSize = null, ?string $dkimSelector = null): bool {
		return $this->client->send(new AddMailDomainDkim($this->user, $domain, $dkimSize, $dkimSelector));
	}

	/**
	 * This function turns off DKIM signing for a mail domain and deletes its key.
	 *
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteDomainDkim(string $domain): bool {
		return $this->client->send(new DeleteMailDomainDkim($this->user, $domain));
	}

	/**
	 * This function makes a mail domain reject messages marked as spam instead of delivering them.
	 *
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addDomainRejectSpam(string $domain): bool {
		return $this->client->send(new AddMailDomainReject($this->user, $domain));
	}

	/**
	 * This function makes a mail domain deliver messages marked as spam again.
	 *
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteDomainRejectSpam(string $domain): bool {
		return $this->client->send(new DeleteMailDomainReject($this->user, $domain));
	}

	/**
	 * This function obtains the SSL certificate of a mail domain.
	 *
	 * @param string $domain
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listDomainSsl(string $domain): array {
		return $this->client->send(new ListMailDomainSsl($this->user, $domain));
	}

	/**
	 * This function turns on SSL for a mail domain (mail.DOMAIN), with a certificate already on the server.
	 *
	 * @param string    $domain
	 * @param string    $sslDir  Directory on the server holding <domain>.crt, <domain>.key and optionally <domain>.ca
	 * @param bool|null $restart Restart the mail services, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addDomainSsl(string $domain, string $sslDir, ?bool $restart = null): bool {
		return $this->client->send(new AddMailDomainSsl($this->user, $domain, $sslDir, $restart));
	}

	/**
	 * This function replaces the SSL certificate of a mail domain with one already on the server.
	 *
	 * @param string    $domain
	 * @param string    $sslDir  Directory on the server holding <domain>.crt, <domain>.key and optionally <domain>.ca
	 * @param bool|null $restart Restart the mail services, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeDomainSslCert(string $domain, string $sslDir, ?bool $restart = null): bool {
		return $this->client->send(new ChangeMailDomainSslCert($this->user, $domain, $sslDir, $restart));
	}

	/**
	 * This function turns off SSL for a mail domain.
	 *
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteDomainSsl(string $domain): bool {
		return $this->client->send(new DeleteMailDomainSsl($this->user, $domain));
	}
}
