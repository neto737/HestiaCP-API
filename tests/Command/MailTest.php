<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use GuzzleHttp\Psr7\Response;
use neto737\HestiaCP\Command\Mail\AddMailAccount;
use neto737\HestiaCP\Command\Mail\AddMailAccountAlias;
use neto737\HestiaCP\Command\Mail\AddMailAccountAutoreply;
use neto737\HestiaCP\Command\Mail\AddMailAccountForward;
use neto737\HestiaCP\Command\Mail\AddMailAccountFwdOnly;
use neto737\HestiaCP\Command\Mail\AddMailDomain;
use neto737\HestiaCP\Command\Mail\ChangeMailAccountPassword;
use neto737\HestiaCP\Command\Mail\DeleteMailAccount;
use neto737\HestiaCP\Command\Mail\DeleteMailAccountAlias;
use neto737\HestiaCP\Command\Mail\DeleteMailAccountAutoreply;
use neto737\HestiaCP\Command\Mail\DeleteMailAccountForward;
use neto737\HestiaCP\Command\Mail\DeleteMailAccountFwdOnly;
use neto737\HestiaCP\Command\Mail\DeleteMailDomain;
use neto737\HestiaCP\Command\Mail\ListMailAccount;
use neto737\HestiaCP\Command\Mail\ListMailAccountAutoreply;
use neto737\HestiaCP\Command\Mail\ListMailAccounts;
use neto737\HestiaCP\Command\Mail\ListMailDomain;
use neto737\HestiaCP\Command\Mail\ListMailDomainDkim;
use neto737\HestiaCP\Command\Mail\ListMailDomainDkimDns;
use neto737\HestiaCP\Command\Mail\ListMailDomains;
use neto737\HestiaCP\Command\Mail\SuspendMailAccount;
use neto737\HestiaCP\Command\Mail\SuspendMailDomain;
use neto737\HestiaCP\Command\Mail\UnsuspendMailAccount;
use neto737\HestiaCP\Command\Mail\UnsuspendMailDomain;

class MailTest extends \PHPUnit\Framework\TestCase {

	public function testListMailDomains(): void {
		$command = new ListMailDomains('admin');
		$this->assertSame('v-list-mail-domains', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testListMailDomain(): void {
		$command = new ListMailDomain('admin', 'example.com');
		$this->assertSame('v-list-mail-domain', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'json'], $command->getRequestParams());

		$this->assertFalse($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testListMailDomainDkim(): void {
		$command = new ListMailDomainDkim('admin', 'domain');
		$this->assertSame('v-list-mail-domain-dkim', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'domain', 'arg3' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testListMailDomainDkimDns(): void {
		$command = new ListMailDomainDkimDns('admin', 'domain');
		$this->assertSame('v-list-mail-domain-dkim-dns', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'domain', 'arg3' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testListMailAccounts(): void {
		$command = new ListMailAccounts('admin', 'domain');
		$this->assertSame('v-list-mail-accounts', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'domain', 'arg3' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
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
				$command->process(new Response());
				$this->fail('Expected an empty response to throw');
			} catch (\neto737\HestiaCP\Exceptions\InvalidResponseException $e) {
				$this->assertSame('Empty response', $e->getMessage());
			}
		}
	}

	public function testAddMailDomain(): void {
		$command = new AddMailDomain('admin', 'domain.com');
		$this->assertSame('v-add-mail-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'yes',
			'arg4' => 'yes',
			'arg5' => 'yes'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testAddMailAccount(): void {
		$command = new AddMailAccount('admin', 'domain.com', 'account', 'pass');
		$this->assertSame('v-add-mail-account', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account',
			'arg4' => 'pass'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testChangeMailAccountPassword(): void {
		$command = new ChangeMailAccountPassword('admin', 'domain', 'account', 'pass');
		$this->assertSame('v-change-mail-account-password', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain',
			'arg3' => 'account',
			'arg4' => 'pass'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testDeleteMailDomain(): void {
		$command = new DeleteMailDomain('admin', 'domain.com');
		$this->assertSame('v-delete-mail-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testDeleteMailAccount(): void {
		$command = new DeleteMailAccount('admin', 'domain.com', 'account');
		$this->assertSame('v-delete-mail-account', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testSuspendMailDomain(): void {
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

	public function testSuspendMailAccount(): void {
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

	public function testUnsuspendMailDomain(): void {
		$command = new UnsuspendMailDomain('admin', 'domain.com');
		$this->assertSame('v-unsuspend-mail-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testUnsuspendMailAccount(): void {
		$command = new UnsuspendMailAccount('admin', 'domain.com', 'account');
		$this->assertSame('v-unsuspend-mail-account', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
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
		(new AddMailAccountAlias('admin', 'example.com', 'info', 'sales'))->process(new Response());
	}
}
