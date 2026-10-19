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
use neto737\HestiaCP\Module\Packages;
use neto737\HestiaCP\Module\Users;
use neto737\HestiaCP\Module\Webs;

/**
 * @phpstan-type ClientOptions array{timeout?: float, verify?: bool|string, handler?: callable}
 */
class Client {

	private \GuzzleHttp\Client $guzzleClient;

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
	public function __construct(private readonly Host $host, ?array $options = null) {
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
	public static function simpleFactory(string $hostname, #[\SensitiveParameter] string $user, #[\SensitiveParameter] ?string $password = null, ?array $options = null): Client {
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
			return $command->process($response);
		} catch (ProcessException | ClientException $e) {
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
			$config = [
				'timeout' => $options['timeout'] ?? 10.0,
				'verify' => $options['verify'] ?? true,
				// HestiaCP reports failures with HTTP error codes, the commands handle them
				'http_errors' => false
			];
			if (isset($options['handler'])) {
				$config['handler'] = $options['handler'];
			}

			$this->guzzleClient = new \GuzzleHttp\Client($config);
		} catch (\Exception $e) {
			throw new ClientException('Bad Client configuration (' . $e->getMessage() . ')');
		}

		return $this;
	}

	public function getModuleUser(): Users {
		return new Users($this);
	}

	public function getModuleMail(string $user): Mails {
		return new Mails($this, $user);
	}

	public function getModuleWeb(string $user): Webs {
		return new Webs($this, $user);
	}

	public function getModuleBackup(string $user): Backups {
		return new Backups($this, $user);
	}

	public function getModuleDatabase(string $user): Databases {
		return new Databases($this, $user);
	}

	public function getModulePackage(): Packages {
		return new Packages($this);
	}

	public function getModuleCron(string $user): Crons {
		return new Crons($this, $user);
	}

	public function getModuleDns(string $user): Dns {
		return new Dns($this, $user);
	}
}
