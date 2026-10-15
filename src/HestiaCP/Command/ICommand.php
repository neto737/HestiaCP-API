<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

use Psr\Http\Message\ResponseInterface;

interface ICommand {

	public function setLastResponse(ResponseInterface $response): void;

	public function getName(): string;

	public function needReturnCode(): bool;

	public function process(): mixed;

	/**
	 * Request timeout in seconds for this command (0 = no timeout), or null to use the client timeout.
	 */
	public function getTimeout(): ?float;

	public function getRequestParams(): array;
}
