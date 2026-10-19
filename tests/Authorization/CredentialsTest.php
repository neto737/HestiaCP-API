<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Authorization;

use neto737\HestiaCP\Authorization\Credentials;

class CredentialsTest extends \PHPUnit\Framework\TestCase {

	public function testParams(): void {
		$this->assertSame(['access_key' => 'access', 'secret_key' => 'secret'], Credentials::accessKey('access', 'secret')->getRequestParams());
		$this->assertSame(['hash' => 'key'], Credentials::legacyHash('key')->getRequestParams());
		$this->assertSame(['user' => 'us', 'password' => 'er'], Credentials::password('us', 'er')->getRequestParams());
	}

	public function testAccessKey(): void {
		$credentials = Credentials::accessKey('access', 'secret');
		$this->assertSame('access', $credentials->getAccessKey());
		$this->assertSame('secret', $credentials->getSecretKey());
		$this->assertNull($credentials->getApiKey());
		$this->assertNull($credentials->getUser());
		$this->assertNull($credentials->getPassword());
	}

	public function testNamedConstructorsOnlySetTheirOwnFields(): void {
		$hash = Credentials::legacyHash('key');
		$this->assertSame('key', $hash->getApiKey());
		$this->assertNull($hash->getUser());
		$this->assertNull($hash->getPassword());
		$this->assertNull($hash->getAccessKey());
		$this->assertNull($hash->getSecretKey());

		$password = Credentials::password('us', 'er');
		$this->assertSame('us', $password->getUser());
		$this->assertSame('er', $password->getPassword());
		$this->assertNull($password->getApiKey());
		$this->assertNull($password->getAccessKey());
		$this->assertNull($password->getSecretKey());
	}

	public function testEmptyValuesKeepTheirMode(): void {
		$this->assertSame(['access_key' => '', 'secret_key' => ''], Credentials::accessKey('', '')->getRequestParams());
		$this->assertSame(['hash' => ''], Credentials::legacyHash('')->getRequestParams());
		$this->assertSame(['user' => '', 'password' => ''], Credentials::password('', '')->getRequestParams());
	}

	public function testDebugInfoMasksSecrets(): void {
		$this->assertSame([
			'mode' => 'access_key',
			'user' => null,
			'password' => null,
			'api_key' => null,
			'access_key' => 'access',
			'secret_key' => '********'
		], Credentials::accessKey('access', 'secret')->__debugInfo());
		$this->assertSame('********', Credentials::legacyHash('key')->__debugInfo()['api_key']);
		$this->assertSame('********', Credentials::password('us', 'er')->__debugInfo()['password']);
		$this->assertSame('us', Credentials::password('us', 'er')->__debugInfo()['user']);
	}

	public function testVarDumpDoesNotShowSecrets(): void {
		ob_start();
		var_dump(Credentials::accessKey('access', 'top-secret'), Credentials::legacyHash('top-hash'), Credentials::password('admin', 'top-password'));
		$dump = (string) ob_get_clean();

		$this->assertStringContainsString('access', $dump);
		$this->assertStringContainsString('admin', $dump);
		$this->assertStringNotContainsString('top-', $dump);
		$this->assertStringNotContainsString('top-', print_r(Credentials::password('admin', 'top-password'), true));
	}

	public function testConstructorIsPrivate(): void {
		$this->assertTrue((new \ReflectionMethod(Credentials::class, '__construct'))->isPrivate());
	}
}
