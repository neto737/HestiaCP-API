<?php

namespace neto737\HestiaCP\Authorization;

class Host {

	/** @var string */
	private $hostname;

	/** @var Credentials */
	private $credentials;

	/** @var int */
	private $port;

	public function __construct(string $hostname, Credentials $credentials, int $port = 8083) {
		$this->setHostname($hostname);
		$this->setCredentials($credentials);
		$this->setPort($port);
	}

	public function setHostname(string $hostname) {
		$this->hostname = $hostname;
		return $this;
	}

	public function getHostname(): string {
		return $this->hostname;
	}

	public function setCredentials(Credentials $credentials) {
		$this->credentials = $credentials;
		return $this;
	}

	public function getCredentials(): Credentials {
		return $this->credentials;
	}

	public function setPort(int $port) {
		$this->port = $port;
		return $this;
	}

	public function getPort(): int {
		return $this->port;
	}

	public function buildUri(): string {
		return $this->getHostname() . ':' . $this->getPort() . '/api/';
	}
}
