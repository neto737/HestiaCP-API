<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use GuzzleHttp\Psr7\Response;
use neto737\HestiaCP\Command\MakeTmpFile;
use neto737\HestiaCP\Command\ResponseCode;
use neto737\HestiaCP\Exceptions\InvalidArgumentException;
use neto737\HestiaCP\Exceptions\InvalidResponseException;
use neto737\HestiaCP\Exceptions\ProcessException;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Responses as sent by HestiaCP 1.10.8's web/api/index.php.
 */
class MakeTmpFileTest extends \PHPUnit\Framework\TestCase {

	public function testCommand(): void {
		$command = new MakeTmpFile('exclusions', "WEB=''\nDB='*'");
		$this->assertSame('v-make-tmp-file', $command->getName());
		$this->assertSame(['arg1' => "WEB=''\nDB='*'", 'arg2' => 'exclusions'], $command->getRequestParams());
		$this->assertSame('/tmp/exclusions', $command->getPath());
		$this->assertFalse($command->needReturnCode());
	}

	public function testSuccessWithAccessKey(): void {
		$this->assertTrue((new MakeTmpFile('f', 'x'))->process(new Response(204, ['Hestia-Exit-Code' => '0'], '')));
	}

	public function testSuccessLegacy(): void {
		$this->assertTrue((new MakeTmpFile('f', 'x'))->process(new Response(200, [], 'OK')));
	}

	public function testError(): void {
		$this->expectException(ProcessException::class);
		$this->expectExceptionCode(ResponseCode::E_INVALID);
		$this->expectExceptionMessage('Error: Invalid temporary file name');
		(new MakeTmpFile('f', 'x'))->process(new Response(422, ['Hestia-Exit-Code' => '2'], 'Error: Invalid temporary file name'));
	}

	public function testNotHestia(): void {
		$this->expectException(InvalidResponseException::class);
		(new MakeTmpFile('f', 'x'))->process(new Response(200, [], '<html>Hello</html>'));
	}

	/**
	 * @return iterable<string, array{string}>
	 */
	public static function invalidNameProvider(): iterable {
		yield 'empty' => [''];
		yield 'dot' => ['.'];
		yield 'dot dot' => ['..'];
		yield 'path' => ['dir/file'];
		yield 'absolute' => ['/tmp/file'];
		yield 'backslash' => ['dir\\file'];
	}

	#[DataProvider('invalidNameProvider')]
	public function testInvalidName(string $name): void {
		$this->expectException(InvalidArgumentException::class);
		new MakeTmpFile($name, 'x');
	}
}
