<?php

namespace heliocg\HestiaCP\Command;

use heliocg\HestiaCP\AuthorizationException;
use heliocg\HestiaCP\InvalidResponseException;
use heliocg\HestiaCP\ProcessException;

class TestAuthorization extends Command {

	public function getName(): string {
		return '';
	}

	public function needReturnCode(): bool {
		return true;
	}

	/**
	 * @return bool
	 * @throws ProcessException
	 */
	public function process(): bool {
		try {
			parent::defaultProcess();
		} catch (AuthorizationException $e) {
			return false;
		}

		if ($this->getResponseCode() === 1) {
			return true;
		}

		throw new InvalidResponseException('Invalid Response. Is host really HestiaCP?');
	}

	public function getRequestParams(): array {
		return [];
	}
}
