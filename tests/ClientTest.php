<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests;

use neto737\HestiaCP\Authorization\Credentials;
use neto737\HestiaCP\Authorization\Host;
use neto737\HestiaCP\Client;

class ClientTest extends \PHPUnit\Framework\TestCase {

	public function testHostWithAPIKey(): void {
		$credentials = Credentials::legacyHash('');
		$host = new Host('https://someHost', $credentials);
		$client = new Client($host);

		$this->assertSame($host, $client->getHost());

		$otherHost = new Host('https://otherHost', $credentials);
		$client->setHost($otherHost);
		$this->assertNotSame($host, $client->getHost());
		$this->assertSame($otherHost, $client->getHost());
	}

	public function testHost(): void {
		$credentials = Credentials::password('', '');
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

	public function testSimpleFactoryWithAccessKey(): void {
		$credentials = Client::simpleFactory('https://someHost', 'ACCESS:SECRET')->getHost()->getCredentials();

		$this->assertSame('ACCESS', $credentials->getAccessKey());
		$this->assertSame('SECRET', $credentials->getSecretKey());
		$this->assertNull($credentials->getApiKey());
	}

	public function testSimpleFactoryWithMoreThanOneColonIsALegacyHash(): void {
		$credentials = Client::simpleFactory('https://someHost', 'a:b:c')->getHost()->getCredentials();

		$this->assertSame('a:b:c', $credentials->getApiKey());
		$this->assertNull($credentials->getAccessKey());
	}

	public function testSimpleFactoryWithPasswordDoesNotSplitTheUser(): void {
		$credentials = Client::simpleFactory('https://someHost', 'us:er', 'pass')->getHost()->getCredentials();

		$this->assertSame('us:er', $credentials->getUser());
		$this->assertNull($credentials->getAccessKey());
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
