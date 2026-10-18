<?php

declare(strict_types=1);

namespace neto737\HestiaCP;

use neto737\HestiaCP\Authorization\Credentials;
use neto737\HestiaCP\Authorization\Host;
use neto737\HestiaCP\Command\ICommand;
use neto737\HestiaCP\Command\TestAuthorization;
use neto737\HestiaCP\Exceptions\ClientException;
use neto737\HestiaCP\Exceptions\InvalidArgumentException;
use neto737\HestiaCP\Exceptions\ProcessException;
use neto737\HestiaCP\Module\Backups;
use neto737\HestiaCP\Module\Crons;
use neto737\HestiaCP\Module\Databases;
use neto737\HestiaCP\Module\Dns;
use neto737\HestiaCP\Module\Mails;
use neto737\HestiaCP\Module\Module;
use neto737\HestiaCP\Module\Packages;
use neto737\HestiaCP\Module\Users;
use neto737\HestiaCP\Module\Webs;

/**
 * @phpstan-type ClientOptions array{timeout?: float, verify?: bool|string, handler?: callable}
 */
class Client {

	private Host $host;

	private \GuzzleHttp\Client $guzzleClient;

	/** @var array<class-string<Module>, array<string, Module>> */
	private array $modules = [];

	/**
	 * Client constructor.
	 *
	 * Options:
	 *  - timeout (float)       Request timeout in seconds, 0 = no timeout (default 10)
	 *  - verify  (bool|string) Verify the TLS certificate, or a path to a CA bundle (default true).
	 *                          Set to false for a server with a self-signed certificate.
	 *  - handler (callable)    Custom Guzzle handler stack (middleware, testing)
	 *
	 * @param Host $host
	 * @param ClientOptions|null $options
	 * @throws ClientException
	 */
	public function __construct(Host $host, ?array $options = null) {
		$this->setHost($host);
		$this->prepareCommunication($options);
	}

	/**
	 * @param string      $hostname	Scheme and host, e.g. https://panel.example.com
	 * @param string      $user		Username, legacy API key, or access key written as ACCESS_KEY:SECRET_KEY
	 * @param string|null $password	Password (must be null if using an API key or access key)
	 * @param ClientOptions|null $options	See Client::__construct()
	 * @return Client
	 * @throws ClientException
	 * @throws InvalidArgumentException
	 */
	public static function simpleFactory(string $hostname, string $user, ?string $password = null, ?array $options = null): Client {
		if (!is_null($password)) {
			$credentials = Credentials::password($user, $password);
		} elseif (substr_count($user, ':') === 1) {
			// Same split HestiaCP applies to a `hash` that holds an access key
			[$accessKey, $secretKey] = explode(':', $user);
			$credentials = Credentials::accessKey($accessKey, $secretKey);
		} else {
			$credentials = Credentials::legacyHash($user);
		}
		$host = new Host($hostname, $credentials);

		return new self($host, $options);
	}

	public function setHost(Host $host): static {
		$this->host = $host;
		return $this;
	}

	public function getHost(): Host {
		return $this->host;
	}

	/**
	 * @return bool
	 * @throws ClientException
	 * @throws ProcessException
	 */
	public function testAuthorization(): bool {
		$command = new TestAuthorization();
		return $this->send($command);
	}

	/**
	 * @template T
	 * @param ICommand<T> $command
	 * @return T
	 * @throws ClientException
	 * @throws ProcessException
	 */
	public function send(ICommand $command): mixed {
		$host = $this->getHost();
		try {
			$response = $this->guzzleClient->post($host->buildUri(), [
				'form_params' => array_merge(
					$host->getCredentials()->getRequestParams(),
					$command->getRequestParams(),
					[
						'returncode' => $command->needReturnCode() ? 'yes' : 'no',
						'cmd' => $command->getName()
					]
				),
				'timeout' => $this->getTimeout($command)
			]);
			$command->setLastResponse($response);
			return $command->process();
		} catch (ProcessException $e) {
			throw $e;
		} catch (\GuzzleHttp\Exception\GuzzleException $e) {
			throw new ClientException('Connection error (' . $e->getMessage() . ')', 0, $e);
		} catch (\Exception $e) {
			throw new ClientException('Fatal processing error (' . $e->getMessage() . ')', 0, $e);
		}
	}

	/**
	 * A command may need a longer timeout than the client default, but never a shorter one.
	 *
	 * @param ICommand<mixed> $command
	 */
	private function getTimeout(ICommand $command): float {
		$timeout = (float) $this->guzzleClient->getConfig('timeout');
		$commandTimeout = $command->getTimeout();

		if ($commandTimeout === null || $timeout == 0) {
			return $timeout;
		}

		return $commandTimeout == 0 ? 0 : max($timeout, $commandTimeout);
	}

	/**
	 * @param ClientOptions|null $options
	 * @return Client
	 * @throws ClientException
	 */
	private function prepareCommunication(?array $options = null): Client {
		try {
			$this->guzzleClient = new \GuzzleHttp\Client([
				'timeout' => $options['timeout'] ?? 10.0,
				'verify' => $options['verify'] ?? true,
				'handler' => $options['handler'] ?? null,
				// HestiaCP reports failures with HTTP error codes, the commands handle them
				'http_errors' => false
			]);
		} catch (\Exception $e) {
			throw new ClientException('Bad Client configuration (' . $e->getMessage() . ')');
		}

		return $this;
	}

	/**
	 * @template T of Module
	 * @param class-string<T> $moduleName
	 * @return T
	 */
	private function loadModule(string $moduleName, ?string $param = null): Module {
		// Modules bound to a user are cached per user, otherwise one user's module would be reused for another
		$key = $param ?? '';

		if (!isset($this->modules[$moduleName][$key])) {
			if ($param !== null) {
				$this->modules[$moduleName][$key] = new $moduleName($this, $param);
			} else {
				$this->modules[$moduleName][$key] = new $moduleName($this);
			}
		}

		return $this->modules[$moduleName][$key];
	}

	public function getModuleUser(): Users {
		return $this->loadModule(Users::class);
	}

	public function getModuleMail(string $user): Mails {
		return $this->loadModule(Mails::class, $user);
	}

	public function getModuleWeb(string $user): Webs {
		return $this->loadModule(Webs::class, $user);
	}

	public function getModuleBackup(string $user): Backups {
		return $this->loadModule(Backups::class, $user);
	}

	public function getModuleDatabase(string $user): Databases {
		return $this->loadModule(Databases::class, $user);
	}

	public function getModulePackage(): Packages {
		return $this->loadModule(Packages::class);
	}

	public function getModuleCron(string $user): Crons {
		return $this->loadModule(Crons::class, $user);
	}

	public function getModuleDns(string $user): Dns {
		return $this->loadModule(Dns::class, $user);
	}
}
