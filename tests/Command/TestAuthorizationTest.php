<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use neto737\HestiaCP\Command\TestAuthorization;
use neto737\HestiaCP\Exceptions\InvalidResponseException;

require_once __DIR__ . '/../../vendor/autoload.php';

class TestAuthorizationTest extends \PHPUnit\Framework\TestCase {

	public function testCommands(): void {
		$command = new TestAuthorization();
		$this->assertSame('v-list-sys-info', $command->getName());
		$this->assertSame([], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());
		$this->assertNull($command->getResponseText());

		$this->expectException(InvalidResponseException::class);
		$command->process();
	}
}
