<?php

namespace neto737\HestiaCP\Command;

use Nette\Utils\ArrayHash;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use neto737\HestiaCP\AuthorizationException;
use neto737\HestiaCP\InvalidResponseException;
use neto737\HestiaCP\ProcessException;

abstract class ListCommand extends Command {

	public function needReturnCode(): bool {
		return false;
	}

	/**
	 * @return array|ArrayHash
	 * @throws ProcessException
	 * @throws AuthorizationException
	 */
	public function process() {
		parent::defaultProcess();

		$responseJson = trim((string) $this->getResponseText());

		// HestiaCP answers with an empty body (HTTP 204) when a command prints nothing
		if ($responseJson === '') {
			return [];
		}

		try {
			$response = Json::decode($responseJson);
		} catch (JsonException $e) {
			throw new ProcessException($responseJson, 0, $e);
		}

		// convert response to array
		return $this->convertList((array)$response);
	}

	protected function convertList(array $list): array {
		foreach ($list as $key => $item) {
			$list[$key] = ArrayHash::from((array) $item);
		}

		return $list;
	}

	/**
	 * @throws InvalidResponseException
	 */
	protected function convertDetail(array $list): ArrayHash {
		if ($list === []) {
			throw new InvalidResponseException('Empty response');
		}

		return array_shift($list);
	}
}
