<?php

use neto737\HestiaCP\Authorization\Credentials;
use neto737\HestiaCP\Authorization\Host;
use neto737\HestiaCP\Client;

require_once __DIR__ . '/../vendor/autoload.php';

class clientTest extends \PHPUnit\Framework\TestCase
{

	public function testHost(): void
	{
		$credentials = new Credentials('', '');
		$host = new Host('', $credentials);
		$client = new Client($host);

		$this->assertSame($host, $client->getHost());

		$otherHost = new Host('otherHost', $credentials);
		$client->setHost($otherHost);
		$this->assertNotSame($host, $client->getHost());
		$this->assertSame($otherHost, $client->getHost());
	}

	public function testSimpleFactory(): void
	{
		$client = Client::simpleFactory('someHost', 'someUser', 'somePass');

		$host = $client->getHost();
		$credentials = $host->getCredentials();

		$this->assertSame('someHost', $host->getHostname());
		$this->assertSame('someUser', $credentials->getUser());
		$this->assertSame('somePass', $credentials->getPassword());
	}

}
