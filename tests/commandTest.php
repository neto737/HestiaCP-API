<?php

use neto737\HestiaCP\Command\TestAuthorization;
use neto737\HestiaCP\InvalidResponseException;
use neto737\HestiaCP\ProcessException;

require_once __DIR__ . '/../vendor/autoload.php';

class commandTest extends \PHPUnit\Framework\TestCase {

	public function testCommands(): void {
		$command = new TestAuthorization();
		$this->assertSame('', $command->getName());
		$this->assertSame([], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(InvalidResponseException::class);
		$command->process();

		$this->assertNull($command->getResponseText());

		$this->expectException(InvalidResponseException::class);
		$this->assertNull($command->getResponseCode());

		$command->defaultProcess();

		$this->expectException(ProcessException::class);
		$command->processException(new \GuzzleHttp\Exception\ClientException('throw!'));
	}
}
