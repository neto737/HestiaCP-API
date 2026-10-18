<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use neto737\HestiaCP\Command\Mail\AddMailAccountAlias;
use neto737\HestiaCP\Command\Mail\AddMailAccountAutoreply;
use neto737\HestiaCP\Command\Mail\AddMailAccountForward;
use neto737\HestiaCP\Command\Mail\AddMailAccountFwdOnly;
use neto737\HestiaCP\Command\Mail\DeleteMailAccountAlias;
use neto737\HestiaCP\Command\Mail\DeleteMailAccountAutoreply;
use neto737\HestiaCP\Command\Mail\DeleteMailAccountForward;
use neto737\HestiaCP\Command\Mail\DeleteMailAccountFwdOnly;
use neto737\HestiaCP\Command\Mail\ListMailAccount;
use neto737\HestiaCP\Command\Mail\ListMailAccountAutoreply;
use neto737\HestiaCP\Command\Mail\ListMailDomain;

class MailTest extends \PHPUnit\Framework\TestCase {

	public function testListMailDomain(): void {
		$command = new ListMailDomain('admin', 'example.com');
		$this->assertSame('v-list-mail-domain', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'json'], $command->getRequestParams());

		$this->assertFalse($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process();
	}

	public function testListMailAccountCommands(): void {
		foreach ([
			'v-list-mail-account' => new ListMailAccount('admin', 'example.com', 'info'),
			'v-list-mail-account-autoreply' => new ListMailAccountAutoreply('admin', 'example.com', 'info')
		] as $name => $command) {
			$this->assertSame($name, $command->getName());
			$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'info', 'arg4' => 'json'], $command->getRequestParams());

			$this->assertFalse($command->needReturnCode());

			try {
				$command->process();
				$this->fail('Expected an empty response to throw');
			} catch (\neto737\HestiaCP\Exceptions\InvalidResponseException $e) {
				$this->assertSame('Empty response', $e->getMessage());
			}
		}
	}

	public function testAccountValueCommands(): void {
		foreach ([
			'v-add-mail-account-alias' => [new AddMailAccountAlias('admin', 'example.com', 'info', 'sales'), 'sales'],
			'v-delete-mail-account-alias' => [new DeleteMailAccountAlias('admin', 'example.com', 'info', 'sales'), 'sales'],
			'v-add-mail-account-forward' => [new AddMailAccountForward('admin', 'example.com', 'info', 'bob@example.org'), 'bob@example.org'],
			'v-delete-mail-account-forward' => [new DeleteMailAccountForward('admin', 'example.com', 'info', 'bob@example.org'), 'bob@example.org'],
			'v-add-mail-account-autoreply' => [new AddMailAccountAutoreply('admin', 'example.com', 'info', "Away until Monday.\nThanks"), "Away until Monday.\nThanks"]
		] as $name => [$command, $value]) {
			$this->assertSame($name, $command->getName());
			$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'info', 'arg4' => $value], $command->getRequestParams());
			$this->assertTrue($command->needReturnCode());
		}
	}

	public function testAccountOnlyCommands(): void {
		foreach ([
			'v-add-mail-account-fwd-only' => new AddMailAccountFwdOnly('admin', 'example.com', 'info'),
			'v-delete-mail-account-fwd-only' => new DeleteMailAccountFwdOnly('admin', 'example.com', 'info'),
			'v-delete-mail-account-autoreply' => new DeleteMailAccountAutoreply('admin', 'example.com', 'info')
		] as $name => $command) {
			$this->assertSame($name, $command->getName());
			$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'info'], $command->getRequestParams());
			$this->assertTrue($command->needReturnCode());
		}
	}

	public function testProcessWithoutResponse(): void {
		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		(new AddMailAccountAlias('admin', 'example.com', 'info', 'sales'))->process();
	}
}
