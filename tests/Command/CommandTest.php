<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use neto737\HestiaCP\Command\ProcessCommand;

class CommandTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @param list<string|null> $args
	 * @return array<string, string>
	 */
	private function positional(array $args): array {
		$command = new class ($args) extends ProcessCommand {

			/**
			 * @param list<string|null> $args
			 */
			public function __construct(private array $args) {}

			public function getName(): string {
				return 'v-test';
			}

			public function getRequestParams(): array {
				return $this->positional($this->args);
			}
		};

		return $command->getRequestParams();
	}

	public function testPositional(): void {
		$this->assertSame([], $this->positional([]));
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $this->positional(['admin', 'json']));
	}

	public function testPositionalDropsTrailingNulls(): void {
		$this->assertSame(['arg1' => 'admin'], $this->positional(['admin', null, null]));
		$this->assertSame([], $this->positional([null, null]));
	}

	public function testPositionalSendsEmptyStringForNullBeforeSetArgument(): void {
		$this->assertSame(['arg1' => 'admin', 'arg2' => '', 'arg3' => 'yes'], $this->positional(['admin', null, 'yes']));
		$this->assertSame(['arg1' => '', 'arg2' => '', 'arg3' => 'yes'], $this->positional([null, null, 'yes', null]));
	}

	public function testPositionalKeepsEmptyStrings(): void {
		$this->assertSame(['arg1' => 'admin', 'arg2' => ''], $this->positional(['admin', '']));
	}

	public function testPositionalHasNoArgumentLimit(): void {
		$args = ['admin', 'domain.com', 'www', 'A', '127.0.0.1', null, null, 'yes', '3600'];
		$params = $this->positional($args);

		$this->assertCount(9, $params);
		$this->assertSame('', $params['arg6']);
		$this->assertSame('3600', $params['arg9']);
	}
}
