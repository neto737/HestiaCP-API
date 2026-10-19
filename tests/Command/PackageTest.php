<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use GuzzleHttp\Psr7\Response;
use neto737\HestiaCP\Command\Package\ListUserPackage;
use neto737\HestiaCP\Command\Package\ListUserPackages;

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
}
