<?php

namespace neto737\HestiaCP\Authorization;

use neto737\HestiaCP\Exceptions\InvalidArgumentException;

class Host {

	/** @var string */
	private $hostname;

	/** @var Credentials */
	private $credentials;

	/** @var int */
	private $port;

	/**
	 * @param string      $hostname		Scheme and host, e.g. https://panel.example.com
	 * @param Credentials $credentials
	 * @param int         $port
	 * @throws InvalidArgumentException
	 */
	public function __construct(string $hostname, Credentials $credentials, int $port = 8083) {
		$this->setHostname($hostname);
		$this->setCredentials($credentials);
		$this->setPort($port);
	}

	/**
	 * The hostname must start with http:// or https://. A trailing slash is removed
	 * and the scheme and host are lowercased. The port is set separately (see setPort()).
	 *
	 * @throws InvalidArgumentException
	 */
	public function setHostname(string $hostname) {
		$this->hostname = self::normalizeHostname($hostname);
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

	/**
	 * @throws InvalidArgumentException
	 */
	private static function normalizeHostname(string $hostname): string {
		$hostname = trim($hostname);

		$authority = preg_replace('~^[a-z][a-z0-9+.-]*://~i', '', $hostname);
		if (filter_var(rtrim($authority, '/'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
			throw new InvalidArgumentException("Invalid hostname '$hostname', an IPv6 address must be in brackets, e.g. 'https://[2001:db8::1]'");
		}

		$parts = parse_url($hostname);

		if ($parts === false || !isset($parts['scheme'], $parts['host']) || $parts['host'] === '') {
			throw new InvalidArgumentException("Invalid hostname '$hostname', expected e.g. 'https://panel.example.com'");
		}

		$scheme = strtolower($parts['scheme']);
		if ($scheme !== 'http' && $scheme !== 'https') {
			throw new InvalidArgumentException("Invalid hostname '$hostname', the scheme must be http or https");
		}

		if (isset($parts['port'])) {
			throw new InvalidArgumentException("Invalid hostname '$hostname', set the port with the port argument instead");
		}

		if (isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment']) || trim($parts['path'] ?? '', '/') !== '') {
			throw new InvalidArgumentException("Invalid hostname '$hostname', it must contain only the scheme and host");
		}

		return $scheme . '://' . strtolower($parts['host']);
	}
}
