<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

use neto737\HestiaCP\Exceptions\AuthorizationException;
use neto737\HestiaCP\Exceptions\InvalidResponseException;
use neto737\HestiaCP\Exceptions\ProcessException;
use Psr\Http\Message\ResponseInterface;

abstract class Command implements ICommand {

	/** @deprecated Use positional() instead. Will be removed in 3.0. */
	protected const ARG_1 = 'arg1';
	/** @deprecated Use positional() instead. Will be removed in 3.0. */
	protected const ARG_2 = 'arg2';
	/** @deprecated Use positional() instead. Will be removed in 3.0. */
	protected const ARG_3 = 'arg3';
	/** @deprecated Use positional() instead. Will be removed in 3.0. */
	protected const ARG_4 = 'arg4';
	/** @deprecated Use positional() instead. Will be removed in 3.0. */
	protected const ARG_5 = 'arg5';
	/** @deprecated Use positional() instead. Will be removed in 3.0. */
	protected const ARG_6 = 'arg6';

	protected const FORMAT_JSON = 'json';

	private ?string $lastResponse = null;

	private ?int $lastStatusCode = null;

	private ?int $lastExitCode = null;

	public function setLastResponse(ResponseInterface $response): void {
		$this->lastResponse = (string) $response->getBody();
		$this->lastStatusCode = $response->getStatusCode();

		$exitCode = $response->getHeaderLine('Hestia-Exit-Code');
		$this->lastExitCode = preg_match('~^\d+$~', $exitCode) ? (int) $exitCode : null;
	}

	public function getResponseText(): ?string {
		return $this->lastResponse;
	}

	public function getStatusCode(): ?int {
		return $this->lastStatusCode;
	}

	/**
	 * Request timeout in seconds for this command (0 = no timeout), or null to use the client timeout.
	 */
	public function getTimeout(): ?float {
		return null;
	}

	/**
	 * @return int
	 * @throws InvalidResponseException
	 */
	public function getResponseCode(): int {
		if ($this->lastExitCode !== null) {
			return $this->lastExitCode;
		}

		$responseText = trim((string) $this->getResponseText());

		if (!preg_match('~^\d+$~', $responseText)) {
			throw new InvalidResponseException('Response is not code. Is hostname really HestiaCP?');
		}

		return (int) $responseText;
	}

	/**
	 * @throws AuthorizationException
	 * @throws ProcessException
	 */
	public function defaultProcess(): void {
		$responseText = (string) $this->getResponseText();

		if (strpos($responseText, 'Error: authentication failed') === 0) {
			throw new AuthorizationException('Authorization failed! Bad user or password', ResponseCode::E_PASSWORD);
		}

		// HestiaCP sends the command exit code in this header and an HTTP error status when it fails
		if ($this->lastExitCode !== null) {
			if ($this->lastExitCode !== ResponseCode::OK) {
				throw $this->codeException($this->lastExitCode);
			}
		} elseif ($this->lastStatusCode !== null && $this->lastStatusCode >= 400) {
			throw new InvalidResponseException('Unexpected HTTP status ' . $this->lastStatusCode . '. Is hostname really HestiaCP?');
		}
	}

	/**
	 * Builds the exception for a non-zero HestiaCP exit code.
	 */
	protected function codeException(int $code): ProcessException {
		$message = ResponseCode::getMessage($code);

		// with returncode=no the body holds HestiaCP's own error message
		$detail = trim((string) $this->getResponseText());
		if ($detail !== '' && !preg_match('~^\d+$~', $detail)) {
			$message .= ' (' . $detail . ')';
		}

		if ($code === ResponseCode::E_PASSWORD || $code === ResponseCode::E_FORBIDDEN) {
			return new AuthorizationException($message, $code);
		}

		return new ProcessException($message, $code);
	}

	/**
	 * Builds the request parameters from the script's arguments, in order: arg1, arg2, …
	 *
	 * HestiaCP passes the arguments by position, so a null argument is left out when nothing after it is set,
	 * and is sent as an empty string otherwise, to keep the arguments after it in their positions.
	 *
	 * @param list<string|null> $args
	 * @return array<string, string>
	 */
	protected function positional(array $args): array {
		while ($args !== [] && end($args) === null) {
			array_pop($args);
		}

		$params = [];
		foreach ($args as $i => $arg) {
			$params['arg' . ($i + 1)] = $arg ?? '';
		}

		return $params;
	}

	/**
	 * @param bool $bool
	 * @return string
	 */
	protected function convertBool(bool $bool): string {
		return $bool === true ? 'yes' : 'no';
	}
}
