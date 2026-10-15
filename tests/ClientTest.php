<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests;

use neto737\HestiaCP\Authorization\Credentials;
use neto737\HestiaCP\Authorization\Host;
use neto737\HestiaCP\Client;

class ClientTest extends \PHPUnit\Framework\TestCase {

	public function testHostWithAPIKey(): void {
		$credentials = new Credentials('');
		$host = new Host('https://someHost', $credentials);
		$client = new Client($host);

		$this->assertSame($host, $client->getHost());

		$otherHost = new Host('https://otherHost', $credentials);
		$client->setHost($otherHost);
		$this->assertNotSame($host, $client->getHost());
		$this->assertSame($otherHost, $client->getHost());
	}

	public function testHost(): void {
		$credentials = new Credentials('', '');
		$host = new Host('https://someHost', $credentials);
		$client = new Client($host);

		$this->assertSame($host, $client->getHost());

		$otherHost = new Host('https://otherHost', $credentials);
		$client->setHost($otherHost);
		$this->assertNotSame($host, $client->getHost());
		$this->assertSame($otherHost, $client->getHost());
	}

	public function testSimpleFactoryWithAPIKey(): void {
		$client = Client::simpleFactory('https://someHost', 'ApiKey');

		$host = $client->getHost();
		$credentials = $host->getCredentials();

		$this->assertSame('https://somehost', $host->getHostname());
		$this->assertSame('ApiKey', $credentials->getApiKey());
	}

	public function testSimpleFactory(): void {
		$client = Client::simpleFactory('https://someHost', 'someUser', 'somePass');

		$host = $client->getHost();
		$credentials = $host->getCredentials();

		$this->assertSame('https://somehost', $host->getHostname());
		$this->assertSame('someUser', $credentials->getUser());
		$this->assertSame('somePass', $credentials->getPassword());
	}
}
