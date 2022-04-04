<?php

use heliocg\HestiaCP\Client;
use heliocg\HestiaCP\Module\Backups;
use heliocg\HestiaCP\Module\Databases;
use heliocg\HestiaCP\Module\Users;
use heliocg\HestiaCP\Module\Webs;
use heliocg\HestiaCP\Module\Mails;

require_once __DIR__ . '/../vendor/autoload.php';

class moduleTest extends \PHPUnit\Framework\TestCase {

	public function createClient(): Client {
		return Client::simpleFactory('someHost', 'someUser', 'somePass');
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

	public function testModuleDb(): void {
		$client = $this->createClient();
		$this->assertSame(Databases::class, get_class($client->getModuleDb('test')));
	}
}
