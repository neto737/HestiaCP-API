<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

use neto737\HestiaCP\Exceptions\AuthorizationException;
use neto737\HestiaCP\Exceptions\InvalidResponseException;
use neto737\HestiaCP\Exceptions\ProcessException;
use Psr\Http\Message\ResponseInterface;

/**
 * @extends Command<array<mixed>>
 */
abstract class ListCommand extends Command {

	public function needReturnCode(): bool {
		return false;
	}

	/**
	 * @return array<mixed>
	 * @throws ProcessException
	 * @throws AuthorizationException
	 * @throws InvalidResponseException
	 */
	public function process(ResponseInterface $response): array {
		parent::defaultProcess($response);

		$responseJson = trim((string) $response->getBody());

		// HestiaCP answers with an empty body (HTTP 204) when a command prints nothing
		if ($responseJson === '') {
			return [];
		}

		// HestiaCP always prints a JSON object or array, so anything else comes from another server
		try {
			$list = json_decode($responseJson, true, 512, JSON_THROW_ON_ERROR);
		} catch (\JsonException $e) {
			throw new InvalidResponseException('Response is not JSON. Is hostname really HestiaCP?', 0, $e);
		}

		if (!is_array($list)) {
			throw new InvalidResponseException('Response is not a JSON object or array. Is hostname really HestiaCP?');
		}

		return $list;
	}

	/**
	 * Returns the only item of a single-object list, e.g. {"admin": {...}}.
	 *
	 * @param array<mixed> $list
	 * @return array<mixed>
	 * @throws InvalidResponseException
	 */
	protected function convertDetail(array $list): array {
		$detail = array_shift($list);

		if (!is_array($detail)) {
			throw new InvalidResponseException('Empty response');
		}

		return $detail;
	}
}
