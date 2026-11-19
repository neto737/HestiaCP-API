<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

use Psr\Http\Message\ResponseInterface;

/**
 * @template-covariant T
 */
interface ICommand {

	public function getName(): string;

	/**
	 * Whether the result is the command's exit code rather than its output. The client then asks HestiaCP
	 * to send the code as the body, except with an access key: HestiaCP always sends the code in the
	 * Hestia-Exit-Code header then, and the body keeps the output or the error message.
	 */
	public function needReturnCode(): bool;

	/**
	 * Turns HestiaCP's response to this command into its result.
	 *
	 * @return T
	 */
	public function process(ResponseInterface $response): mixed;

	/**
	 * Request timeout in seconds for this command (0 = no timeout), or null to use the client timeout.
	 */
	public function getTimeout(): ?float;

	/**
	 * @return array<string, string>
	 */
	public function getRequestParams(): array;
}
