<?php

declare(strict_types=1);

use neto737\HestiaCP\Client;
use neto737\HestiaCP\Module\Backups;
use neto737\HestiaCP\Module\Databases;
use neto737\HestiaCP\Module\Users;
use neto737\HestiaCP\Module\Webs;
use neto737\HestiaCP\Module\Mails;

require_once __DIR__ . '/../vendor/autoload.php';

class moduleTest extends \PHPUnit\Framework\TestCase {

	public function createClient(): Client {
		return Client::simpleFactory('https://someHost', 'someUser', 'somePass');
	}

	public function testModuleUser(): void {
		$client = $this->createClient();
		$this->assertSame(Users::class, get_class($client->getModuleUser()));
	}

	public function testModuleWeb(): void {
		$client = $this->createClient();
		$this->assertSame(Webs::class, get_class($client->getModuleWeb('test')));
	}

	public function testModuleMail(): void {
		$client = $this->createClient();
		$this->assertSame(Mails::class, get_class($client->getModuleMail('test')));
	}

	public function testModuleBackup(): void {
		$client = $this->createClient();
		$this->assertSame(Backups::class, get_class($client->getModuleBackup('test')));
	}

	public function testDatabases(): void {
		$client = $this->createClient();
		$this->assertSame(Databases::class, get_class($client->getModuleDatabase('test')));
	}

	public function testModuleCachedPerUser(): void {
		$client = $this->createClient();

		$this->assertSame($client->getModuleUser(), $client->getModuleUser());

		$this->assertSame($client->getModuleMail('alice'), $client->getModuleMail('alice'));
		$this->assertNotSame($client->getModuleMail('alice'), $client->getModuleMail('bob'));
		$this->assertNotSame($client->getModuleWeb('alice'), $client->getModuleWeb('bob'));
		$this->assertNotSame($client->getModuleBackup('alice'), $client->getModuleBackup('bob'));
		$this->assertNotSame($client->getModuleDatabase('alice'), $client->getModuleDatabase('bob'));
	}
}
