<?php

namespace neto737\HestiaCP\Command;

use neto737\HestiaCP\AuthorizationException;
use neto737\HestiaCP\InvalidResponseException;
use neto737\HestiaCP\ProcessException;
use Psr\Http\Message\ResponseInterface;

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
