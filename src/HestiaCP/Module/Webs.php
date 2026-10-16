<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Module;

use neto737\HestiaCP\Client;
use neto737\HestiaCP\Command\Web\AddLetsEncryptDomain;
use neto737\HestiaCP\Command\Web\AddWebDomain;
use neto737\HestiaCP\Command\Web\AddWebDomainAlias;
use neto737\HestiaCP\Command\Web\AddWebDomainFtp;
use neto737\HestiaCP\Command\Web\AddWebDomainProxy;
use neto737\HestiaCP\Command\Web\AddWebDomainSsl;
use neto737\HestiaCP\Command\Web\AddWebDomainSslForce;
use neto737\HestiaCP\Command\Web\AddWebDomainSslHsts;
use neto737\HestiaCP\Command\Web\ChangeWebDomainBackendTpl;
use neto737\HestiaCP\Command\Web\ChangeWebDomainFtpPassword;
use neto737\HestiaCP\Command\Web\ChangeWebDomainFtpPath;
use neto737\HestiaCP\Command\Web\ChangeWebDomainProxyTpl;
use neto737\HestiaCP\Command\Web\ChangeWebDomainSslCert;
use neto737\HestiaCP\Command\Web\ChangeWebDomainSslHome;
use neto737\HestiaCP\Command\Web\ChangeWebDomainTpl;
use neto737\HestiaCP\Command\Web\SuspendWebDomain;
use neto737\HestiaCP\Command\Web\UnsuspendWebDomain;
use neto737\HestiaCP\Command\Web\DeleteLetsEncryptDomain;
use neto737\HestiaCP\Command\Web\DeleteWebDomain;
use neto737\HestiaCP\Command\Web\DeleteWebDomainAlias;
use neto737\HestiaCP\Command\Web\DeleteWebDomainFtp;
use neto737\HestiaCP\Command\Web\DeleteWebDomainProxy;
use neto737\HestiaCP\Command\Web\DeleteWebDomainSsl;
use neto737\HestiaCP\Command\Web\DeleteWebDomainSslForce;
use neto737\HestiaCP\Command\Web\DeleteWebDomainSslHsts;
use neto737\HestiaCP\Command\Web\ListWebDomain;
use neto737\HestiaCP\Command\Web\ListWebDomainSsl;
use neto737\HestiaCP\Command\Web\ListWebDomains;
use neto737\HestiaCP\Command\Web\ListWebTemplates;
use neto737\HestiaCP\Command\Web\ListWebTemplatesBackend;
use neto737\HestiaCP\Command\Web\ListWebTemplatesProxy;

class Webs extends Module {

	private string $user;

	public function __construct(Client $client, string $user) {
		parent::__construct($client);
		$this->user = $user;
	}

	/**
	 * This function adds virtual host to a server.
	 *
	 * @param string      $domain
	 * @param string|null $ip
	 * @param string|null $aliases
	 * @param string|null $proxyExtensions
	 * @param bool        $restart
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addDomain(string $domain, ?string $ip = null, ?string $aliases = null, ?string $proxyExtensions = null, bool $restart = false): bool {
		return $this->client->send(new AddWebDomain($this->user, $domain, $ip, $aliases, $proxyExtensions, $restart));
	}

	/**
	 * This function for suspending the site's operation.
	 *
	 * @param string      $domain
	 * @param bool        $restart
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function suspendDomain(string $domain, bool $restart = false): bool {
		return $this->client->send(new SuspendWebDomain($this->user, $domain, $restart));
	}

	/**
	 * This function of unsuspending the domain.
	 *
	 * @param string      $domain
	 * @param bool        $restart
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function unsuspendDomain(string $domain, bool $restart = false): bool {
		return $this->client->send(new UnsuspendWebDomain($this->user, $domain, $restart));
	}

	/**
	 * This call of function leads to the removal of domain and all its components (statistics, folders contents, ssl certificates, etc.).
	 *
	 * @param string $domain
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteDomain(string $domain): bool {
		return $this->client->send(new DeleteWebDomain($this->user, $domain));
	}

	/**
	 * This function check and validates domain with Let's Encrypt
	 *
	 * @param string      $domain
	 * @param string|null $aliases
	 * @param bool        $mail    Issue the certificate for the mail domain instead of the web domain
	 * @param string|null $keyAlgo One of the AddLetsEncryptDomain::KEY_ALGO_* constants. Needs a HestiaCP version newer than 1.10.5.
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 * @throws \neto737\HestiaCP\Exceptions\InvalidArgumentException
	 */
	public function addDomainLetsEncrypt(string $domain, ?string $aliases = null, bool $mail = false, ?string $keyAlgo = null): bool {
		return $this->client->send(new AddLetsEncryptDomain($this->user, $domain, $aliases, $mail, $keyAlgo));
	}

	/**
	 * This function turns off letsencrypt SSL support for a domain.
	 *
	 * @param string $domain
	 * @param bool   $restart
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteDomainLetsEncrypt(string $domain, bool $restart = false): bool {
		return $this->client->send(new DeleteLetsEncryptDomain($this->user, $domain, $restart));
	}

	/**
	 * This function creates additional ftp account for web domain.
	 *
	 * @param string      $domain
	 * @param string      $ftpUser
	 * @param string      $ftpPassword
	 * @param string|null $ftpPath
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addDomainFtp(string $domain, string $ftpUser, string $ftpPassword, ?string $ftpPath = null): bool {
		return $this->client->send(new AddWebDomainFtp($this->user, $domain, $ftpUser, $ftpPassword, $ftpPath));
	}

	/**
	 * This function changes ftp user password.
	 *
	 * @param string      $domain
	 * @param string      $ftpUser
	 * @param string      $ftpPassword
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeDomainFtpPassword(string $domain, string $ftpUser, string $ftpPassword): bool {
		return $this->client->send(new ChangeWebDomainFtpPassword($this->user, $domain, $ftpUser, $ftpPassword));
	}

	/**
	 * This function changes ftp user path.
	 *
	 * @param string $domain
	 * @param string $ftpUser
	 * @param string $ftpPath
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeDomainFtpPath(string $domain, string $ftpUser, string $ftpPath): bool {
		return $this->client->send(new ChangeWebDomainFtpPath($this->user, $domain, $ftpUser, $ftpPath));
	}

	/**
	 * This function deletes additional ftp account.
	 *
	 * @param string $domain
	 * @param string $ftpUser
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteDomainFtp(string $domain, string $ftpUser): bool {
		return $this->client->send(new DeleteWebDomainFtp($this->user, $domain, $ftpUser));
	}

	/**
	 * This function to obtain the list of all user web domains.
	 *
	 * @return array<array<mixed>>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listDomains(): array {
		return $this->client->send(new ListWebDomains($this->user));
	}

	/**
	 * This function obtains the parameters of a web domain.
	 *
	 * @param string $domain
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listDomain(string $domain): array {
		return $this->client->send(new ListWebDomain($this->user, $domain));
	}

	/**
	 * This function adds one or more aliases to a web domain.
	 *
	 * @param string    $domain
	 * @param string    $aliases Comma-separated
	 * @param bool|null $restart Restart the web server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addDomainAlias(string $domain, string $aliases, ?bool $restart = null): bool {
		return $this->client->send(new AddWebDomainAlias($this->user, $domain, $aliases, $restart));
	}

	/**
	 * This function deletes an alias from a web domain.
	 *
	 * @param string    $domain
	 * @param string    $alias
	 * @param bool|null $restart Restart the web server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteDomainAlias(string $domain, string $alias, ?bool $restart = null): bool {
		return $this->client->send(new DeleteWebDomainAlias($this->user, $domain, $alias, $restart));
	}

	/**
	 * This function obtains the SSL certificate of a web domain.
	 *
	 * @param string $domain
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listDomainSsl(string $domain): array {
		return $this->client->send(new ListWebDomainSsl($this->user, $domain));
	}

	/**
	 * This function turns on SSL for a web domain with a certificate already on the server.
	 *
	 * @param string      $domain
	 * @param string      $sslDir  Directory on the server holding <domain>.crt, <domain>.key and optionally <domain>.ca
	 * @param string|null $sslHome One of the AddWebDomainSsl::SSL_HOME_* constants, or null for HestiaCP's default (same)
	 * @param bool|null   $restart Restart the web server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 * @throws \neto737\HestiaCP\Exceptions\InvalidArgumentException
	 */
	public function addDomainSsl(string $domain, string $sslDir, ?string $sslHome = null, ?bool $restart = null): bool {
		return $this->client->send(new AddWebDomainSsl($this->user, $domain, $sslDir, $sslHome, $restart));
	}

	/**
	 * This function replaces the SSL certificate of a web domain with one already on the server.
	 *
	 * @param string    $domain
	 * @param string    $sslDir  Directory on the server holding <domain>.crt, <domain>.key and optionally <domain>.ca
	 * @param bool|null $restart Restart the web server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeDomainSslCert(string $domain, string $sslDir, ?bool $restart = null): bool {
		return $this->client->send(new ChangeWebDomainSslCert($this->user, $domain, $sslDir, $restart));
	}

	/**
	 * This function changes the directory HTTPS serves for a web domain.
	 *
	 * @param string    $domain
	 * @param string    $sslHome One of the AddWebDomainSsl::SSL_HOME_* constants
	 * @param bool|null $restart Restart the web server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 * @throws \neto737\HestiaCP\Exceptions\InvalidArgumentException
	 */
	public function changeDomainSslHome(string $domain, string $sslHome, ?bool $restart = null): bool {
		return $this->client->send(new ChangeWebDomainSslHome($this->user, $domain, $sslHome, $restart));
	}

	/**
	 * This function turns off SSL for a web domain and deletes its certificate.
	 *
	 * @param string    $domain
	 * @param bool|null $restart Restart the web server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteDomainSsl(string $domain, ?bool $restart = null): bool {
		return $this->client->send(new DeleteWebDomainSsl($this->user, $domain, $restart));
	}

	/**
	 * This function redirects HTTP to HTTPS for a web domain.
	 *
	 * @param string    $domain
	 * @param bool|null $restart Restart the web server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addDomainSslForce(string $domain, ?bool $restart = null): bool {
		return $this->client->send(new AddWebDomainSslForce($this->user, $domain, $restart));
	}

	/**
	 * This function stops redirecting HTTP to HTTPS for a web domain.
	 *
	 * @param string    $domain
	 * @param bool|null $restart Restart the web server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteDomainSslForce(string $domain, ?bool $restart = null): bool {
		return $this->client->send(new DeleteWebDomainSslForce($this->user, $domain, $restart));
	}

	/**
	 * This function turns on HSTS for a web domain.
	 *
	 * @param string    $domain
	 * @param bool|null $restart Restart the web server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addDomainSslHsts(string $domain, ?bool $restart = null): bool {
		return $this->client->send(new AddWebDomainSslHsts($this->user, $domain, $restart));
	}

	/**
	 * This function turns off HSTS for a web domain.
	 *
	 * @param string    $domain
	 * @param bool|null $restart Restart the web server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteDomainSslHsts(string $domain, ?bool $restart = null): bool {
		return $this->client->send(new DeleteWebDomainSslHsts($this->user, $domain, $restart));
	}

	/**
	 * This function obtains the list of web server templates.
	 *
	 * @return array<mixed> Template names
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listTemplates(): array {
		return $this->client->send(new ListWebTemplates());
	}

	/**
	 * This function obtains the list of backend (PHP-FPM) templates.
	 *
	 * @return array<mixed> Template names
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listBackendTemplates(): array {
		return $this->client->send(new ListWebTemplatesBackend());
	}

	/**
	 * This function obtains the list of proxy templates.
	 *
	 * @return array<mixed> Template names
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listProxyTemplates(): array {
		return $this->client->send(new ListWebTemplatesProxy());
	}

	/**
	 * This function changes the web server template of a web domain.
	 *
	 * @param string    $domain
	 * @param string    $template
	 * @param bool|null $restart  Restart the web server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeDomainTemplate(string $domain, string $template, ?bool $restart = null): bool {
		return $this->client->send(new ChangeWebDomainTpl($this->user, $domain, $template, $restart));
	}

	/**
	 * This function changes the backend (PHP-FPM) template of a web domain.
	 *
	 * @param string    $domain
	 * @param string    $template
	 * @param bool|null $restart  Restart the web server and backend, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeDomainBackendTemplate(string $domain, string $template, ?bool $restart = null): bool {
		return $this->client->send(new ChangeWebDomainBackendTpl($this->user, $domain, $template, $restart));
	}

	/**
	 * This function changes the proxy template of a web domain.
	 *
	 * @param string      $domain
	 * @param string      $template
	 * @param string|null $extensions File extensions served by the proxy, comma-separated, or null for HestiaCP's default list
	 * @param bool|null   $restart    Restart the proxy server, or null for HestiaCP's default. Needs $extensions.
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 * @throws \neto737\HestiaCP\Exceptions\InvalidArgumentException
	 */
	public function changeDomainProxyTemplate(string $domain, string $template, ?string $extensions = null, ?bool $restart = null): bool {
		return $this->client->send(new ChangeWebDomainProxyTpl($this->user, $domain, $template, $extensions, $restart));
	}

	/**
	 * This function turns on the proxy for a web domain.
	 *
	 * @param string      $domain
	 * @param string|null $template   Proxy template, or null for the user's package default
	 * @param string|null $extensions File extensions served by the proxy, comma-separated, or null for HestiaCP's default list
	 * @param bool|null   $restart    Restart the proxy server, or null for HestiaCP's default. Needs $extensions.
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 * @throws \neto737\HestiaCP\Exceptions\InvalidArgumentException
	 */
	public function addDomainProxy(string $domain, ?string $template = null, ?string $extensions = null, ?bool $restart = null): bool {
		return $this->client->send(new AddWebDomainProxy($this->user, $domain, $template, $extensions, $restart));
	}

	/**
	 * This function turns off the proxy for a web domain.
	 *
	 * @param string    $domain
	 * @param bool|null $restart Restart the proxy server, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteDomainProxy(string $domain, ?bool $restart = null): bool {
		return $this->client->send(new DeleteWebDomainProxy($this->user, $domain, $restart));
	}
}
