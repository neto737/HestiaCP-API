<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests;

use neto737\HestiaCP\Exceptions\AuthorizationException;
use neto737\HestiaCP\Exceptions\ClientException;
use neto737\HestiaCP\Exceptions\Exception;
use neto737\HestiaCP\Exceptions\InvalidArgumentException;
use neto737\HestiaCP\Exceptions\InvalidResponseException;
use neto737\HestiaCP\Exceptions\ProcessException;
use neto737\HestiaCP\Exceptions\RuntimeException;
use PHPUnit\Framework\Attributes\DataProvider;

class ExceptionsTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @return array<string, array{\Throwable, class-string}>
	 */
	public static function exceptions(): array {
		return [
			'InvalidArgumentException' => [new InvalidArgumentException(), \InvalidArgumentException::class],
			'RuntimeException' => [new RuntimeException(), \RuntimeException::class],
			'ProcessException' => [new ProcessException(), \RuntimeException::class],
			'AuthorizationException' => [new AuthorizationException(), \RuntimeException::class],
			'ClientException' => [new ClientException(), \RuntimeException::class],
			'InvalidResponseException' => [new InvalidResponseException(), \RuntimeException::class],
		];
	}

	/**
	 * @param class-string $splClass
	 */
	#[DataProvider('exceptions')]
	public function testExtendsSplExceptionAndImplementsMarker(\Throwable $exception, string $splClass): void {
		$this->assertInstanceOf($splClass, $exception);
		$this->assertInstanceOf(Exception::class, $exception);
	}
}
