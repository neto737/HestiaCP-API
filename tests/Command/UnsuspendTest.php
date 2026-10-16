<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use neto737\HestiaCP\Command\User\UnsuspendUser;
use neto737\HestiaCP\Command\Web\UnsuspendWebDomain;
use neto737\HestiaCP\Command\Mail\UnsuspendMailAccount;
use neto737\HestiaCP\Command\Mail\UnsuspendMailDomain;
use neto737\HestiaCP\Command\Database\UnsuspendDatabase;

class UnsuspendTest extends \PHPUnit\Framework\TestCase {

	public function testUser(): void {
		$command = new UnsuspendUser('admin');
		$this->assertSame('v-unsuspend-user', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'no'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process();
	}

	public function testWebDomain(): void {
		$command = new UnsuspendWebDomain('admin', 'domain.com');
		$this->assertSame('v-unsuspend-web-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'no'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process();
	}

	public function testMailAccount(): void {
		$command = new UnsuspendMailAccount('admin', 'domain.com', 'account');
		$this->assertSame('v-unsuspend-mail-account', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process();
	}

	public function testMailDomain(): void {
		$command = new UnsuspendMailDomain('admin', 'domain.com');
		$this->assertSame('v-unsuspend-mail-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process();
	}

	public function testDatabase(): void {
		$command = new UnsuspendDatabase('admin', 'admin_db');
		$this->assertSame('v-unsuspend-database', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'admin_db'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process();
	}
}
