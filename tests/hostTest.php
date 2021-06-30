<?php

use neto737\HestiaCP\Authorization\Credentials;
use neto737\HestiaCP\Authorization\Host;

require_once __DIR__ . '/../vendor/autoload.php';

class hostTest extends \PHPUnit\Framework\TestCase {

	public function testHost() {
		$credentials = new Credentials('', '');
		$host = new Host('', $credentials);

		$this->assertSame('', $host->getHostname());

		$host->setHostname('host');
		$this->assertSame('host', $host->getHostname());
	}

	public function testUri() {
		$credentials = new Credentials('', '');
		$host = new Host('https://hestia', $credentials);

		$this->assertSame('https://hestia:8083/api/', $host->buildUri());
	}

	public function testPort() {
		$credentials = new Credentials('', '');
		$host = new Host('', $credentials);

		$this->assertSame(8083, $host->getPort());

		$host->setPort(2023);
		$this->assertSame(2023, $host->getPort());
	}

	public function testCredentials() {
		$credentials = new Credentials('', '');
		$host = new Host('', $credentials);

		$this->assertSame($credentials, $host->getCredentials());

		$newCredentials = new Credentials('', '');
		$host->setCredentials($newCredentials);

		$this->assertNotSame($credentials, $host->getCredentials());
		$this->assertSame($newCredentials, $host->getCredentials());
	}
}
