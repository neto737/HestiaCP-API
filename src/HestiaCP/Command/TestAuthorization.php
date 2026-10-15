<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

use neto737\HestiaCP\Exceptions\AuthorizationException;
use neto737\HestiaCP\Exceptions\InvalidResponseException;
use neto737\HestiaCP\Exceptions\ProcessException;

/**
 * Runs a harmless read-only command to check the credentials.
 * HestiaCP rejects an empty command before checking access keys, so a real command is needed.
 */
class TestAuthorization extends Command {

	public function getName(): string {
		return 'v-list-sys-info';
	}

	public function needReturnCode(): bool {
		return true;
	}

	/**
	 * @return bool
	 * @throws InvalidResponseException
	 * @throws ProcessException
	 */
	public function process(): bool {
		try {
			parent::defaultProcess();

			$code = $this->getResponseCode();
			if ($code === ResponseCode::OK) {
				return true;
			}

			throw $this->codeException($code);
		} catch (AuthorizationException $e) {
			return false;
		}
	}

	public function getRequestParams(): array {
		return [];
	}
}
