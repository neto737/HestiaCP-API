<?php

use neto737\HestiaCP\Command\TestAuthorization;
use neto737\HestiaCP\InvalidResponseException;

require_once __DIR__ . '/../vendor/autoload.php';

class commandTest extends \PHPUnit\Framework\TestCase {

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
