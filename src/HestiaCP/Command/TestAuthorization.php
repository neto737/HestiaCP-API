<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

use neto737\HestiaCP\Exceptions\AuthorizationException;
use neto737\HestiaCP\Exceptions\InvalidResponseException;
use neto737\HestiaCP\Exceptions\ProcessException;
use Psr\Http\Message\ResponseInterface;

/**
 * Runs a harmless read-only command to check the credentials.
 * HestiaCP rejects an empty command before checking access keys, so a real command is needed.
 */
class TestAuthorization extends ProcessCommand {

	public function getName(): string {
		return 'v-list-sys-info';
	}

	/**
	 * @return bool false when HestiaCP rejects the credentials
	 * @throws InvalidResponseException
	 * @throws ProcessException
	 */
	#[\Override]
	public function process(ResponseInterface $response): bool {
		try {
			return parent::process($response);
		} catch (AuthorizationException) {
			return false;
		}
	}

	public function getRequestParams(): array {
		return [];
	}
}
