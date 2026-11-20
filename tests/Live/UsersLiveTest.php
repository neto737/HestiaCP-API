<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Live;

use neto737\HestiaCP\Command\RawCommand;
use neto737\HestiaCP\Command\RawListCommand;
use neto737\HestiaCP\Command\ResponseCode;
use neto737\HestiaCP\Exceptions\ProcessException;

class UsersLiveTest extends LiveTestCase {

	public function testAuthorization(): void {
		$this->assertTrue(static::$client->testAuthorization());
	}

	public function testListAndDetail(): void {
		$users = static::$client->getModuleUser();

		$this->assertArrayHasKey(self::user(), $users->list());
		$this->assertSame(self::user() . '@example.net', $users->detail(self::user())['CONTACT']);
		foreach ($users->stats(self::user()) as $month) {
			$this->assertArrayHasKey('PACKAGE', $month);
		}
	}

	public function testChanges(): void {
		$users = static::$client->getModuleUser();

		$this->assertTrue($users->changePassword(self::user(), self::password()));
		$this->assertTrue($users->changeContact(self::user(), 'new-' . self::user() . '@example.net'));
		$this->assertSame('new-' . self::user() . '@example.net', $users->detail(self::user())['CONTACT']);
		$this->assertTrue($users->changePackage(self::user(), 'default'));
	}

	public function testSuspend(): void {
		$users = static::$client->getModuleUser();

		$this->assertTrue($users->suspend(self::user()));
		$this->assertSame('yes', $users->detail(self::user())['SUSPENDED']);
		$this->assertTrue($users->unsuspend(self::user()));
		$this->assertSame('no', $users->detail(self::user())['SUSPENDED']);
	}

	public function testErrorsCarryTheExitCodeAndHestiaMessage(): void {
		try {
			static::$client->getModuleUser()->add(self::user(), self::password(), 'x@example.net');
			$this->fail('ProcessException expected');
		} catch (ProcessException $e) {
			$this->assertSame(ResponseCode::E_EXISTS, $e->getCode());
		}

		try {
			static::$client->getModuleUser()->delete('nouser' . self::user());
			$this->fail('ProcessException expected');
		} catch (ProcessException $e) {
			$this->assertSame(ResponseCode::E_NOTEXIST, $e->getCode());
			// with an access key, the body holds HestiaCP's message
			$this->assertStringContainsString("doesn't exist", $e->getMessage());
		}

		$this->expectException(ProcessException::class);
		$this->expectExceptionCode(ResponseCode::E_NOTEXIST);
		static::$client->getModuleUser()->detail('nouser' . self::user());
	}

	public function testPackages(): void {
		$packages = static::$client->getModulePackage();

		$this->assertArrayHasKey('default', $packages->list());
		$this->assertArrayHasKey('WEB_DOMAINS', $packages->detail('default'));
	}

	public function testProfile(): void {
		$users = static::$client->getModuleUser();

		$this->assertTrue($users->changeName(self::user(), 'Ada', 'Lovelace'));
		$this->assertSame('Ada Lovelace', $users->detail(self::user())['NAME']);
		$this->assertTrue($users->changeLanguage(self::user(), 'de'));
		$this->assertSame('de', $users->detail(self::user())['LANGUAGE']);
		$this->assertTrue($users->changeShell(self::user(), 'bash'));
		$this->assertSame('bash', $users->detail(self::user())['SHELL']);
		$this->assertTrue($users->changeShell(self::user(), 'nologin'));
		$this->assertTrue($users->changeRole(self::user(), 'admin'));
		$this->assertSame('admin', $users->detail(self::user())['ROLE']);
		$this->assertTrue($users->changeRole(self::user(), 'user'));
		$this->assertSame('user', $users->detail(self::user())['ROLE']);
	}

	public function testRawCommands(): void {
		$this->assertArrayHasKey('sysinfo', static::$client->send(new RawListCommand('v-list-sys-info', ['json'])));
		$this->assertTrue(static::$client->send(new RawCommand('v-change-user-name', [self::user(), 'Raw', 'Name'])));
		$this->assertSame('Raw Name', static::$client->getModuleUser()->detail(self::user())['NAME']);
	}
}
