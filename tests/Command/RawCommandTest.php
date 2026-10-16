<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use neto737\HestiaCP\Client;
use neto737\HestiaCP\Command\RawCommand;
use neto737\HestiaCP\Command\RawListCommand;
use neto737\HestiaCP\Command\ResponseCode;
use neto737\HestiaCP\Exceptions\InvalidArgumentException;
use neto737\HestiaCP\Exceptions\ProcessException;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @phpstan-type HistoryEntry array{request: RequestInterface, response: ResponseInterface|null, error: mixed, options: array<array-key, mixed>}
 */
class RawCommandTest extends \PHPUnit\Framework\TestCase {

	/** @var array<array-key, HistoryEntry> */
	private array $history = [];

	private function createClient(Response $response): Client {
		$this->history = [];
		$stack = HandlerStack::create(new MockHandler([$response]));
		$stack->push(Middleware::history($this->history));

		return Client::simpleFactory('https://someHost', 'ACCESS:SECRET', null, ['handler' => $stack]);
	}

	/**
	 * @return array<string, mixed>
	 */
	private function sentParams(): array {
		$this->assertCount(1, $this->history);
		parse_str((string) $this->history[0]['request']->getBody(), $params);

		return $params;
	}

	public function testRawCommand(): void {
		$command = new RawCommand('v-add-cron-job', ['admin', '0', '3', '*', '*', '*', 'echo hi']);
		$this->assertSame('v-add-cron-job', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => '0',
			'arg3' => '3',
			'arg4' => '*',
			'arg5' => '*',
			'arg6' => '*',
			'arg7' => 'echo hi'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());
	}

	public function testRawListCommand(): void {
		$command = new RawListCommand('v-list-cron-jobs', ['admin', 'json']);
		$this->assertSame('v-list-cron-jobs', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertFalse($command->needReturnCode());
	}

	public function testWithoutArguments(): void {
		$this->assertSame([], (new RawCommand('v-update-sys-queue'))->getRequestParams());
		$this->assertSame([], (new RawListCommand('v-list-sys-info'))->getRequestParams());
	}

	public function testNullArguments(): void {
		$command = new RawCommand('v-add-dns-record', ['admin', 'example.com', 'www', 'A', '10.0.0.1', null, null, 'yes', null]);

		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'example.com',
			'arg3' => 'www',
			'arg4' => 'A',
			'arg5' => '10.0.0.1',
			'arg6' => '',
			'arg7' => '',
			'arg8' => 'yes'
		], $command->getRequestParams());
	}

	public function testArgumentKeysAreIgnored(): void {
		$command = new RawCommand('v-suspend-database', ['user' => 'admin', 'database' => 'admin_db']);

		$this->assertSame(['arg1' => 'admin', 'arg2' => 'admin_db'], $command->getRequestParams());
	}

	/**
	 * @return iterable<string, array{string}>
	 */
	public static function invalidNameProvider(): iterable {
		yield 'empty' => [''];
		yield 'no prefix' => ['list-users'];
		yield 'prefix only' => ['v-'];
		yield 'uppercase' => ['v-List-Users'];
		yield 'trailing dash' => ['v-list-users-'];
		yield 'double dash' => ['v-list--users'];
		yield 'space' => ['v-list-users json'];
		yield 'path' => ['../v-list-users'];
	}

	#[DataProvider('invalidNameProvider')]
	public function testInvalidNameThrows(string $name): void {
		$this->expectException(InvalidArgumentException::class);
		new RawCommand($name);
	}

	#[DataProvider('invalidNameProvider')]
	public function testInvalidListNameThrows(string $name): void {
		$this->expectException(InvalidArgumentException::class);
		new RawListCommand($name);
	}

	public function testSendRawCommand(): void {
		$client = $this->createClient(new Response(200, ['Hestia-Exit-Code' => '0'], '0'));

		$this->assertTrue($client->send(new RawCommand('v-suspend-database', ['admin', 'admin_db'])));
		$this->assertSame([
			'access_key' => 'ACCESS',
			'secret_key' => 'SECRET',
			'arg1' => 'admin',
			'arg2' => 'admin_db',
			'returncode' => 'yes',
			'cmd' => 'v-suspend-database'
		], $this->sentParams());
	}

	public function testSendRawCommandFailure(): void {
		$client = $this->createClient(new Response(400, ['Hestia-Exit-Code' => '3'], '3'));

		$this->expectException(ProcessException::class);
		$this->expectExceptionCode(ResponseCode::E_NOTEXIST);
		$client->send(new RawCommand('v-suspend-database', ['admin', 'admin_db']));
	}

	public function testSendRawListCommand(): void {
		$client = $this->createClient(new Response(200, ['Hestia-Exit-Code' => '0'], '{"1": {"MIN": "0", "HOUR": "3"}}'));

		$this->assertSame(['1' => ['MIN' => '0', 'HOUR' => '3']], $client->send(new RawListCommand('v-list-cron-jobs', ['admin', 'json'])));
		$this->assertSame([
			'access_key' => 'ACCESS',
			'secret_key' => 'SECRET',
			'arg1' => 'admin',
			'arg2' => 'json',
			'returncode' => 'no',
			'cmd' => 'v-list-cron-jobs'
		], $this->sentParams());
	}
}
