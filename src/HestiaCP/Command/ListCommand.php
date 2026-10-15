<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

use neto737\HestiaCP\Exceptions\AuthorizationException;
use neto737\HestiaCP\Exceptions\InvalidResponseException;
use neto737\HestiaCP\Exceptions\ProcessException;

abstract class ListCommand extends Command {

	public function needReturnCode(): bool {
		return false;
	}

	/**
	 * @return array
	 * @throws ProcessException
	 * @throws AuthorizationException
	 */
	public function process(): array {
		parent::defaultProcess();

		$responseJson = trim((string) $this->getResponseText());

		// HestiaCP answers with an empty body (HTTP 204) when a command prints nothing
		if ($responseJson === '') {
			return [];
		}

		try {
			$response = json_decode($responseJson, true, 512, JSON_THROW_ON_ERROR);
		} catch (\JsonException $e) {
			throw new ProcessException($responseJson, 0, $e);
		}

		if (!is_array($response)) {
			throw new ProcessException($responseJson);
		}

		return $response;
	}

	/**
	 * Returns the only item of a single-object list, e.g. {"admin": {...}}.
	 *
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
