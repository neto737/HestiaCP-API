<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Authorization;

use neto737\HestiaCP\Authorization\Credentials;
use neto737\HestiaCP\Authorization\Host;
use neto737\HestiaCP\Exceptions\InvalidArgumentException;

class HostTest extends \PHPUnit\Framework\TestCase {

	public function testHost(): void {
		$credentials = new Credentials('', '');
		$host = new Host('https://hestia', $credentials);

		$this->assertSame('https://hestia', $host->getHostname());

		$host->setHostname('http://host');
		$this->assertSame('http://host', $host->getHostname());
	}

	/** @return array<string, array{string, string}> */
	public static function normalizedHostnames(): array {
		return [
			'trailing slash' => ['https://hestia/', 'https://hestia'],
			'several trailing slashes' => ['https://hestia///', 'https://hestia'],
			'surrounding whitespace' => ["  https://hestia \n", 'https://hestia'],
			'uppercase scheme and host' => ['HTTPS://Panel.Example.COM', 'https://panel.example.com'],
			'ip address' => ['https://192.0.2.10', 'https://192.0.2.10'],
			'ipv6 address' => ['https://[2001:db8::1]/', 'https://[2001:db8::1]'],
		];
	}

	#[\PHPUnit\Framework\Attributes\DataProvider('normalizedHostnames')]
	public function testNormalizesHostname(string $hostname, string $expected): void {
		$host = new Host($hostname, new Credentials('', ''));

		$this->assertSame($expected, $host->getHostname());
	}

	/** @return array<string, array{string}> */
	public static function invalidHostnames(): array {
		return [
			'empty' => [''],
			'missing scheme' => ['hestia'],
			'missing scheme with port' => ['hestia:8083'],
			'scheme only' => ['https://'],
			'unsupported scheme' => ['ftp://hestia'],
			'port' => ['https://hestia:8083'],
			'ip address with port' => ['https://192.0.2.10:8083'],
			'ipv6 address with port' => ['https://[2001:db8::1]:8083'],
			'path' => ['https://hestia/api/'],
			'query' => ['https://hestia?a=b'],
			'fragment' => ['https://hestia#top'],
			'user info' => ['https://user:pass@hestia'],
		];
	}

	#[\PHPUnit\Framework\Attributes\DataProvider('invalidHostnames')]
	public function testRejectsInvalidHostname(string $hostname): void {
		$this->expectException(InvalidArgumentException::class);

		new Host($hostname, new Credentials('', ''));
	}

	public function testRejectsUnbracketedIpv6(): void {
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('must be in brackets');

		new Host('https://2001:db8::1', new Credentials('', ''));
	}

	public function testSetHostnameKeepsPreviousValueOnError(): void {
		$host = new Host('https://hestia', new Credentials('', ''));

		try {
			$host->setHostname('hestia');
			$this->fail('Expected InvalidArgumentException');
		} catch (InvalidArgumentException $e) {
			$this->assertSame('https://hestia', $host->getHostname());
		}
	}

	public function testUri(): void {
		$credentials = new Credentials('', '');
		$host = new Host('https://hestia', $credentials);

		$this->assertSame('https://hestia:8083/api/', $host->buildUri());

		$host = new Host('https://hestia/', $credentials, 2083);
		$this->assertSame('https://hestia:2083/api/', $host->buildUri());
	}

	public function testPort(): void {
		$credentials = new Credentials('', '');
		$host = new Host('https://hestia', $credentials);

		$this->assertSame(8083, $host->getPort());

		$host->setPort(2023);
		$this->assertSame(2023, $host->getPort());
	}

	public function testCredentials(): void {
		$credentials = new Credentials('', '');
		$host = new Host('https://hestia', $credentials);

		$this->assertSame($credentials, $host->getCredentials());

		$newCredentials = new Credentials('', '');
		$host->setCredentials($newCredentials);

		$this->assertNotSame($credentials, $host->getCredentials());
		$this->assertSame($newCredentials, $host->getCredentials());
	}
}
