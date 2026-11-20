<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use GuzzleHttp\Psr7\Response;
use neto737\HestiaCP\Command\Package\ListUserPackage;
use neto737\HestiaCP\Command\Package\ListUserPackages;
use neto737\HestiaCP\Command\Package\AddUserPackage;
use neto737\HestiaCP\Command\Package\CopyUserPackage;
use neto737\HestiaCP\Command\Package\DeleteUserPackage;
use neto737\HestiaCP\Command\Package\RenameUserPackage;

class PackageTest extends \PHPUnit\Framework\TestCase {

	public function testListUserPackages(): void {
		$command = new ListUserPackages();
		$this->assertSame('v-list-user-packages', $command->getName());
		$this->assertSame(['arg1' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testListUserPackage(): void {
		$command = new ListUserPackage('default');
		$this->assertSame('v-list-user-package', $command->getName());
		$this->assertSame(['arg1' => 'default', 'arg2' => 'json'], $command->getRequestParams());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	/**
	 * @return iterable<string, array{\neto737\HestiaCP\Command\ICommand<mixed>, array<string, string>}>
	 */
	public static function changeProvider(): iterable {
		yield 'v-add-user-package' => [new AddUserPackage('/tmp/gold.pkg', 'gold'), ['arg1' => '/tmp/gold.pkg', 'arg2' => 'gold']];
		yield 'v-add-user-package rewrite' => [new AddUserPackage('/tmp/gold.pkg', 'gold', true), ['arg1' => '/tmp/gold.pkg', 'arg2' => 'gold', 'arg3' => 'yes']];
		yield 'v-copy-user-package' => [new CopyUserPackage('gold', 'silver'), ['arg1' => 'gold', 'arg2' => 'silver']];
		yield 'v-rename-user-package' => [new RenameUserPackage('gold', 'platinum'), ['arg1' => 'gold', 'arg2' => 'platinum']];
		yield 'v-delete-user-package' => [new DeleteUserPackage('gold'), ['arg1' => 'gold']];
	}

	/**
	 * @param \neto737\HestiaCP\Command\ICommand<mixed> $command
	 * @param array<string, string> $params
	 */
	#[\PHPUnit\Framework\Attributes\DataProvider('changeProvider')]
	public function testChangeCommands(\neto737\HestiaCP\Command\ICommand $command, array $params): void {
		$this->assertSame(explode(' ', (string) $this->dataName())[0], $command->getName());
		$this->assertSame($params, $command->getRequestParams());
		$this->assertTrue($command->needReturnCode());
	}
}
