<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Authorization;

use neto737\HestiaCP\Authorization\Credentials;

require_once __DIR__ . '/../../vendor/autoload.php';

class CredentialsTest extends \PHPUnit\Framework\TestCase {

	public function testApiKey(): void {
		$credentials = new Credentials('');
		$this->assertSame('', $credentials->getApiKey());

		$credentials->setApiKey('key');
		$this->assertSame('key', $credentials->getApiKey());
	}

	public function testUser(): void {
		$credentials = new Credentials('', '');
		$this->assertSame('', $credentials->getUser());

		$credentials->setUser('test');
		$this->assertSame('test', $credentials->getUser());
	}

	public function testPassword(): void {
		$credentials = new Credentials('', '');
		$this->assertSame('', $credentials->getPassword());

		$credentials->setPassword('test');
		$this->assertSame('test', $credentials->getPassword());
	}

	public function testParams(): void {
		$credentials = new Credentials('us', 'er');
		$this->assertSame(['user' => 'us', 'password' => 'er'], $credentials->getRequestParams());

		$credentials_apikey = new Credentials('key');
		$this->assertSame(['hash' => 'key'], $credentials_apikey->getRequestParams());

		$credentials_accesskey = Credentials::accessKey('access', 'secret');
		$this->assertSame(['access_key' => 'access', 'secret_key' => 'secret'], $credentials_accesskey->getRequestParams());
	}

	public function testAccessKey(): void {
		$credentials = Credentials::accessKey('access', 'secret');
		$this->assertSame('access', $credentials->getAccessKey());
		$this->assertSame('secret', $credentials->getSecretKey());
		$this->assertNull($credentials->getApiKey());
		$this->assertNull($credentials->getUser());
		$this->assertNull($credentials->getPassword());
	}

	public function testSwitchBetweenKeys(): void {
		$credentials = Credentials::accessKey('access', 'secret');
		$credentials->setApiKey('key');
		$this->assertNull($credentials->getAccessKey());
		$this->assertNull($credentials->getSecretKey());
		$this->assertSame(['hash' => 'key'], $credentials->getRequestParams());

		$credentials->setAccessKey('access', 'secret');
		$this->assertNull($credentials->getApiKey());
		$this->assertSame(['access_key' => 'access', 'secret_key' => 'secret'], $credentials->getRequestParams());
	}

	public function testNamedConstructors(): void {
		$this->assertSame(['hash' => 'key'], Credentials::legacyHash('key')->getRequestParams());
		$this->assertSame(['user' => 'us', 'password' => 'er'], Credentials::password('us', 'er')->getRequestParams());
	}
}
