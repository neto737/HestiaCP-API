<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

use neto737\HestiaCP\Exceptions\AuthorizationException;
use neto737\HestiaCP\Exceptions\InvalidResponseException;
use neto737\HestiaCP\Exceptions\ProcessException;
use Psr\Http\Message\ResponseInterface;

/**
 * @template-covariant T
 * @implements ICommand<T>
 */
abstract class Command implements ICommand {

	protected const FORMAT_JSON = 'json';

	/**
	 * Request timeout in seconds for this command (0 = no timeout), or null to use the client timeout.
	 */
	public function getTimeout(): ?float {
		return null;
	}

	/**
	 * @throws InvalidResponseException
	 */
	protected function getResponseCode(ResponseInterface $response): int {
		$exitCode = $this->getExitCode($response);
		if ($exitCode !== null) {
			return $exitCode;
		}

		$responseText = trim((string) $response->getBody());

		if (!preg_match('~^\d+$~', $responseText)) {
			throw new InvalidResponseException('Response is not code. Is hostname really HestiaCP?');
		}

		return (int) $responseText;
	}

	/**
	 * @throws AuthorizationException
	 * @throws ProcessException
	 */
	protected function defaultProcess(ResponseInterface $response): void {
		$responseText = (string) $response->getBody();

		if (strpos($responseText, 'Error: authentication failed') === 0) {
			throw new AuthorizationException('Authorization failed! Bad user or password', ResponseCode::E_PASSWORD);
		}

		// HestiaCP sends the command exit code in this header and an HTTP error status when it fails
		$exitCode = $this->getExitCode($response);
		$statusCode = $response->getStatusCode();
		if ($exitCode !== null) {
			if ($exitCode !== ResponseCode::OK) {
				throw $this->codeException($exitCode, $response);
			}
		} elseif ($statusCode >= 400) {
			throw new InvalidResponseException('Unexpected HTTP status ' . $statusCode . '. Is hostname really HestiaCP?');
		}
	}

	/**
	 * Builds the exception for a non-zero HestiaCP exit code.
	 */
	protected function codeException(int $code, ResponseInterface $response): ProcessException {
		$message = ResponseCode::getMessage($code);

		// with returncode=no the body holds HestiaCP's own error message
		$detail = trim((string) $response->getBody());
		if ($detail !== '' && !preg_match('~^\d+$~', $detail)) {
			$message .= ' (' . $detail . ')';
		}

		if ($code === ResponseCode::E_PASSWORD || $code === ResponseCode::E_FORBIDDEN) {
			return new AuthorizationException($message, $code);
		}

		return new ProcessException($message, $code);
	}

	/**
	 * The exit code from the Hestia-Exit-Code header, or null when the response has none.
	 */
	private function getExitCode(ResponseInterface $response): ?int {
		$exitCode = $response->getHeaderLine('Hestia-Exit-Code');

		return preg_match('~^\d+$~', $exitCode) ? (int) $exitCode : null;
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
