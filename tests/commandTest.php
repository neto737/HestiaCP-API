<?php
require_once __DIR__ . '/../vendor/autoload.php';

class commandTest extends \PHPUnit\Framework\TestCase
{

	public function testCommands(): void
	{
		$command = new \neto737\HestiaCP\Command\TestAuthorization();
		$this->assertSame('', $command->getName());
		$this->assertSame([], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\InvalidResponseException::class);
		$command->process();

		$this->assertNull($command->getResponseText());

		$this->expectException(\neto737\HestiaCP\InvalidResponseException::class);
		$this->assertNull($command->getResponseCode());

		$command->defaultProcess();

		$this->expectException(\neto737\HestiaCP\ProcessException::class);
		$command->processException(new \GuzzleHttp\Exception\ClientException('throw!'));
	}

}
