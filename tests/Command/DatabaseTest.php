<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use GuzzleHttp\Psr7\Response;
use neto737\HestiaCP\Command\Database\AddDatabase;
use neto737\HestiaCP\Command\Database\ChangeDatabasePassword;
use neto737\HestiaCP\Command\Database\DeleteDatabase;
use neto737\HestiaCP\Command\Database\DeleteDatabases;
use neto737\HestiaCP\Command\Database\ListDatabase;
use neto737\HestiaCP\Command\Database\ListDatabases;
use neto737\HestiaCP\Command\Database\SuspendDatabase;
use neto737\HestiaCP\Command\Database\UnsuspendDatabase;

class DatabaseTest extends \PHPUnit\Framework\TestCase {

	public function testListDatabase(): void {
		$command = new ListDatabase('admin', 'test');
		$this->assertSame('v-list-database', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'test', 'arg3' => 'json'], $command->getRequestParams());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testListDatabases(): void {
		$command = new ListDatabases('admin');
		$this->assertSame('v-list-databases', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testAddDatabase(): void {
		$command = new AddDatabase('admin', 'database', 'user', 'pass', 'mysql');
		$this->assertSame('v-add-database', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'database',
			'arg3' => 'user',
			'arg4' => 'pass',
			'arg5' => 'mysql'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testChangeDatabasePassword(): void {
		$command = new ChangeDatabasePassword('admin', 'admin_db', 'pass');
		$this->assertSame('v-change-database-password', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'admin_db',
			'arg3' => 'pass'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testDeleteDatabase(): void {
		$command = new DeleteDatabase('admin', 'admin_test');
		$this->assertSame('v-delete-database', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'admin_test'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testDeleteDatabases(): void {
		$command = new DeleteDatabases('admin');
		$this->assertSame('v-delete-databases', $command->getName());
		$this->assertSame([
			'arg1' => 'admin'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testSuspendDatabase(): void {
		$command = new SuspendDatabase('admin', 'admin_db');
		$this->assertSame('v-suspend-database', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'admin_db'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testUnsuspendDatabase(): void {
		$command = new UnsuspendDatabase('admin', 'admin_db');
		$this->assertSame('v-unsuspend-database', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'admin_db'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}
}
