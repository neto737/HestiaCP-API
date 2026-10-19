<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use GuzzleHttp\Psr7\Response;
use neto737\HestiaCP\Command\User\SuspendUser;
use neto737\HestiaCP\Command\Web\SuspendWebDomain;
use neto737\HestiaCP\Command\Mail\SuspendMailAccount;
use neto737\HestiaCP\Command\Mail\SuspendMailDomain;
use neto737\HestiaCP\Command\Database\SuspendDatabase;

class SuspendTest extends \PHPUnit\Framework\TestCase {

	public function testUser(): void {
		$command = new SuspendUser('admin');
		$this->assertSame('v-suspend-user', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'no'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testWebDomain(): void {
		$command = new SuspendWebDomain('admin', 'domain.com');
		$this->assertSame('v-suspend-web-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'no'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testMailAccount(): void {
		$command = new SuspendMailAccount('admin', 'domain.com', 'account');
		$this->assertSame('v-suspend-mail-account', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testMailDomain(): void {
		$command = new SuspendMailDomain('admin', 'domain.com');
		$this->assertSame('v-suspend-mail-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testDatabase(): void {
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
}
