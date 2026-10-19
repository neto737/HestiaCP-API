<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests;

use neto737\HestiaCP\Authorization\Credentials;
use neto737\HestiaCP\Client;
use neto737\HestiaCP\Command\Database\AddDatabase;
use neto737\HestiaCP\Command\Database\ChangeDatabasePassword;
use neto737\HestiaCP\Command\Mail\AddMailAccount;
use neto737\HestiaCP\Command\Mail\ChangeMailAccountPassword;
use neto737\HestiaCP\Command\User\AddUser;
use neto737\HestiaCP\Command\User\ChangeUserPassword;
use neto737\HestiaCP\Command\Web\AddWebDomainFtp;
use neto737\HestiaCP\Command\Web\ChangeWebDomainFtpPassword;
use neto737\HestiaCP\Exceptions\InvalidArgumentException;
use neto737\HestiaCP\Module\Databases;
use neto737\HestiaCP\Module\Mails;
use neto737\HestiaCP\Module\Users;
use neto737\HestiaCP\Module\Webs;
use PHPUnit\Framework\Attributes\DataProvider;
use SensitiveParameter;
use SensitiveParameterValue;

class SensitiveParameterTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @return array<string, array{class-string, string, list<string>}>
	 */
	public static function sensitiveParameters(): array {
		return [
			'Credentials::__construct' => [Credentials::class, '__construct', ['password', 'api_key', 'secret_key']],
			'Credentials::accessKey' => [Credentials::class, 'accessKey', ['secretKey']],
			'Credentials::legacyHash' => [Credentials::class, 'legacyHash', ['hash']],
			'Credentials::password' => [Credentials::class, 'password', ['password']],
			'Client::simpleFactory' => [Client::class, 'simpleFactory', ['user', 'password']],
			'Users::add' => [Users::class, 'add', ['password']],
			'Users::changePassword' => [Users::class, 'changePassword', ['password']],
			'Mails::addAccount' => [Mails::class, 'addAccount', ['password']],
			'Mails::changeAccountPassword' => [Mails::class, 'changeAccountPassword', ['password']],
			'Databases::add' => [Databases::class, 'add', ['dbpass']],
			'Databases::changePassword' => [Databases::class, 'changePassword', ['dbpass']],
			'Webs::addDomainFtp' => [Webs::class, 'addDomainFtp', ['ftpPassword']],
			'Webs::changeDomainFtpPassword' => [Webs::class, 'changeDomainFtpPassword', ['ftpPassword']],
			'AddUser' => [AddUser::class, '__construct', ['password']],
			'ChangeUserPassword' => [ChangeUserPassword::class, '__construct', ['password']],
			'AddMailAccount' => [AddMailAccount::class, '__construct', ['password']],
			'ChangeMailAccountPassword' => [ChangeMailAccountPassword::class, '__construct', ['password']],
			'AddDatabase' => [AddDatabase::class, '__construct', ['dbpass']],
			'ChangeDatabasePassword' => [ChangeDatabasePassword::class, '__construct', ['dbpass']],
			'AddWebDomainFtp' => [AddWebDomainFtp::class, '__construct', ['ftpPassword']],
			'ChangeWebDomainFtpPassword' => [ChangeWebDomainFtpPassword::class, '__construct', ['ftpPassword']]
		];
	}

	/**
	 * @param class-string $class
	 * @param list<string> $sensitive
	 */
	#[DataProvider('sensitiveParameters')]
	public function testOnlySecretsAreMarked(string $class, string $method, array $sensitive): void {
		$marked = [];
		foreach ((new \ReflectionMethod($class, $method))->getParameters() as $parameter) {
			if ($parameter->getAttributes(SensitiveParameter::class) !== []) {
				$marked[] = $parameter->getName();
			}
		}

		$this->assertSame($sensitive, $marked);
	}

	public function testSimpleFactoryHidesSecretsFromTheTrace(): void {
		if (ini_get('zend.exception_ignore_args')) {
			$this->markTestSkipped('zend.exception_ignore_args is on, so traces hold no arguments');
		}

		try {
			Client::simpleFactory('not a hostname', 'admin', 'hunter2');
			$this->fail('Expected an InvalidArgumentException');
		} catch (InvalidArgumentException $e) {
			$frames = array_values(array_filter($e->getTrace(), static fn (array $frame): bool => $frame['function'] === 'simpleFactory'));
			$this->assertCount(1, $frames);

			$args = $frames[0]['args'] ?? [];
			$this->assertSame('not a hostname', $args[0]);
			$this->assertInstanceOf(SensitiveParameterValue::class, $args[1]);
			$this->assertInstanceOf(SensitiveParameterValue::class, $args[2]);
			$this->assertStringNotContainsString('hunter2', $e->getTraceAsString());
		}
	}
}
