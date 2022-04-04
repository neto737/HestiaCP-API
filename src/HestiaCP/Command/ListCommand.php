<?php

namespace heliocg\HestiaCP\Command;

use Nette\Utils\ArrayHash;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use heliocg\HestiaCP\AuthorizationException;
use heliocg\HestiaCP\ProcessException;

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

		$responseJson = $this->getResponseText();
		try {
			$response = Json::decode($responseJson);
		} catch (JsonException $e) {
			throw new ProcessException($responseJson);
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

	protected function convertDetail(array $list): ArrayHash {
		return array_shift($list);
	}
}
