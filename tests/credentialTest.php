<?php

use neto737\HestiaCP\Authorization\Credentials;

require_once __DIR__ . '/../vendor/autoload.php';

class credentialTest extends \PHPUnit\Framework\TestCase {

	public function testApiKey() {
		$credentials = new Credentials('');
		$this->assertSame('', $credentials->getApiKey());

		$credentials->setApiKey('key');
		$this->assertSame('key', $credentials->getApiKey());
	}

	public function testUser() {
		$credentials = new Credentials('', '');
		$this->assertSame('', $credentials->getUser());

		$credentials->setUser('test');
		$this->assertSame('test', $credentials->getUser());
	}

	public function testPassword() {
		$credentials = new Credentials('', '');
		$this->assertSame('', $credentials->getPassword());

		$credentials->setPassword('test');
		$this->assertSame('test', $credentials->getPassword());
	}

	public function testParams() {
		$credentials = new Credentials('us', 'er');
		$this->assertSame(['user' => 'us', 'password' => 'er'], $credentials->getRequestParams());

		$credentials_apikey = new Credentials('key');
		$this->assertSame(['hash' => 'key'], $credentials_apikey->getRequestParams());
	}
}
