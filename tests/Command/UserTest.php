<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use GuzzleHttp\Psr7\Response;
use neto737\HestiaCP\Command\User\AddUser;
use neto737\HestiaCP\Command\User\ChangeUserContact;
use neto737\HestiaCP\Command\User\ChangeUserPackage;
use neto737\HestiaCP\Command\User\ChangeUserPassword;
use neto737\HestiaCP\Command\User\DeleteUser;
use neto737\HestiaCP\Command\User\ListUser;
use neto737\HestiaCP\Command\User\ListUsers;
use neto737\HestiaCP\Command\User\ListUserStats;
use neto737\HestiaCP\Command\User\SuspendUser;
use neto737\HestiaCP\Command\User\UnsuspendUser;

class UserTest extends \PHPUnit\Framework\TestCase {

	public function testListUser(): void {
		$command = new ListUser('admin');
		$this->assertSame('v-list-user', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertFalse($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testListUsers(): void {
		$command = new ListUsers();
		$this->assertSame('v-list-users', $command->getName());
		$this->assertSame(['arg1' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testListUserStats(): void {
		$command = new ListUserStats('admin');
		$this->assertSame('v-list-user-stats', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testAddUser(): void {
		$command = new AddUser('admin', 'pass', 'mail@domain.com');
		$this->assertSame('v-add-user', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'pass', 'arg3' => 'mail@domain.com', 'arg4' => 'default'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testAddUserWithName(): void {
		$command = new AddUser('admin', 'pass', 'mail@domain.com', null, 'John', 'Doe');
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'pass',
			'arg3' => 'mail@domain.com',
			'arg4' => 'default',
			'arg5' => 'John',
			'arg6' => 'Doe'
		], $command->getRequestParams());

		$command = new AddUser('admin', 'pass', 'mail@domain.com', 'custom', 'John');
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'pass',
			'arg3' => 'mail@domain.com',
			'arg4' => 'custom',
			'arg5' => 'John'
		], $command->getRequestParams());

		$command = new AddUser('admin', 'pass', 'mail@domain.com', null, null, 'Doe');
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'pass',
			'arg3' => 'mail@domain.com',
			'arg4' => 'default',
			'arg5' => '',
			'arg6' => 'Doe'
		], $command->getRequestParams());
	}

	public function testChangeUserPassword(): void {
		$command = new ChangeUserPassword('admin', 'pass');
		$this->assertSame('v-change-user-password', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'pass'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testChangeUserContact(): void {
		$command = new ChangeUserContact('admin', 'admin@example.com');
		$this->assertSame('v-change-user-contact', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'admin@example.com'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testChangeUserPackage(): void {
		$command = new ChangeUserPackage('admin', 'gold');
		$this->assertSame('v-change-user-package', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'gold',
			'arg3' => 'no'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testDeleteUser(): void {
		$command = new DeleteUser('admin');
		$this->assertSame('v-delete-user', $command->getName());
		$this->assertSame(['arg1' => 'admin'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testSuspendUser(): void {
		$command = new SuspendUser('admin');
		$this->assertSame('v-suspend-user', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'no'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testUnsuspendUser(): void {
		$command = new UnsuspendUser('admin');
		$this->assertSame('v-unsuspend-user', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'no'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}
}
