<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

use neto737\HestiaCP\Exceptions\InvalidArgumentException;

/**
 * The script name and arguments shared by {@see RawCommand} and {@see RawListCommand}.
 *
 * @internal
 */
trait RawArguments {

	private readonly string $name;

	/** @var list<string|null> */
	private readonly array $args;

	/**
	 * @param string $name The script name, e.g. v-list-users
	 * @param array<string|null> $args The script's arguments, in order; keys are ignored (see positional())
	 * @throws InvalidArgumentException
	 */
	public function __construct(string $name, array $args = []) {
		// HestiaCP's scripts are all named v-<lowercase words>
		if (!preg_match('~^v-[a-z0-9]+(-[a-z0-9]+)*$~', $name)) {
			throw new InvalidArgumentException("Invalid command name '$name', expected a HestiaCP script name such as 'v-list-users'");
		}

		$this->name = $name;
		$this->args = array_values($args);
	}

	public function getName(): string {
		return $this->name;
	}

	public function getRequestParams(): array {
		return $this->positional($this->args);
	}
}
