<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Live;

use neto737\HestiaCP\Client;
use neto737\HestiaCP\Command\ResponseCode;
use neto737\HestiaCP\Exceptions\InvalidResponseException;
use neto737\HestiaCP\Exceptions\ProcessException;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The legacy endpoint (legacy API key or password) answers differently from the access key endpoint:
 * no exit code header, HTTP 200 and the error text when a list command fails.
 */
class EndpointLiveTest extends LiveTestCase {

	/**
	 * @return iterable<string, array{string}>
	 */
	public static function legacyProvider(): iterable {
		yield 'legacy API key' => ['hash'];
		yield 'password' => ['password'];
	}

	private static function legacyClient(string $mode): Client {
		$secret = getenv($mode === 'hash' ? 'HESTIACP_HASH' : 'HESTIACP_PASSWORD');
		if ($secret === false) {
			self::markTestSkipped('Set ' . ($mode === 'hash' ? 'HESTIACP_HASH' : 'HESTIACP_PASSWORD') . ' to test the legacy endpoint');
		}

		return $mode === 'hash' ? self::createClient($secret) : self::createClient('admin', $secret);
	}

	#[DataProvider('legacyProvider')]
	public function testLegacyCommands(string $mode): void {
		$client = self::legacyClient($mode);

		$this->assertTrue($client->testAuthorization());
		$this->assertArrayHasKey(self::user(), $client->getModuleUser()->list());
		$this->assertTrue($client->getModuleUser()->changeContact(self::user(), $mode . '-' . self::user() . '@example.net'));
	}

	#[DataProvider('legacyProvider')]
	public function testLegacyProcessErrorHasTheExitCode(string $mode): void {
		$this->expectException(ProcessException::class);
		$this->expectExceptionCode(ResponseCode::E_NOTEXIST);
		self::legacyClient($mode)->getModuleUser()->delete('nouser' . self::user());
	}

	#[DataProvider('legacyProvider')]
	public function testLegacyListErrorHasHestiaMessage(string $mode): void {
		try {
			self::legacyClient($mode)->getModuleUser()->detail('nouser' . self::user());
			$this->fail('ProcessException expected');
		} catch (ProcessException $e) {
			// the legacy endpoint sends no exit code for list commands
			$this->assertSame(0, $e->getCode());
			$this->assertStringContainsString("doesn't exist", $e->getMessage());
		}
	}

	#[DataProvider('legacyProvider')]
	public function testLegacyBackupExclusions(string $mode): void {
		$backup = self::legacyClient($mode)->getModuleBackup(self::user());

		$this->assertTrue($backup->updateExclusions(['DB' => ['*']]));
		$this->assertArrayHasKey('*', $backup->listBackupExclusions()['DB']);
		$this->assertTrue($backup->deleteExclusions());
	}

	public function testHttpIsNotFollowedToHttps(): void {
		$host = (string) getenv('HESTIACP_HOST');
		if (!str_starts_with($host, 'https://')) {
			$this->markTestSkipped('HESTIACP_HOST must use https://');
		}

		$client = Client::simpleFactory('http://' . substr($host, 8), (string) getenv('HESTIACP_ACCESS_KEY'), null, ['timeout' => 30]);

		$this->expectException(InvalidResponseException::class);
		$this->expectExceptionMessage('Unexpected redirect');
		$client->getModuleUser()->list();
	}
}
