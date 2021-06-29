<?php

use neto737\HestiaCP\Authorization\Credentials;
use neto737\HestiaCP\Authorization\Host;
use neto737\HestiaCP\Client;

require_once __DIR__ . '/../vendor/autoload.php';

class moduleTest extends \PHPUnit\Framework\TestCase
{

	public function createClient(): Client
	{
		return Client::simpleFactory('someHost', 'someUser', 'somePass');
	}

	public function testModuleUser(): void
	{
		$client = $this->createClient();
		$this->assertSame(\neto737\HestiaCP\Module\Users::class, get_class($client->getModuleUser()));
	}

	public function testModuleWeb(): void
	{
		$client = $this->createClient();
		$this->assertSame(\neto737\HestiaCP\Module\Webs::class, get_class($client->getModuleWeb('test')));
	}

	public function testModuleMail(): void
	{
		$client = $this->createClient();
		$this->assertSame(\neto737\HestiaCP\Module\Mails::class, get_class($client->getModuleMail('test')));
	}

}
