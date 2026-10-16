<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

use Psr\Http\Message\ResponseInterface;

/**
 * @template-covariant T
 */
interface ICommand {

	public function setLastResponse(ResponseInterface $response): void;

	public function getName(): string;

	public function needReturnCode(): bool;

	/**
	 * @return T
	 */
	public function process(): mixed;

	/**
	 * Request timeout in seconds for this command (0 = no timeout), or null to use the client timeout.
	 */
	public function getTimeout(): ?float;

	/**
	 * @return array<string, string>
	 */
	public function getRequestParams(): array;
}
