<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

use neto737\HestiaCP\Exceptions\AuthorizationException;
use neto737\HestiaCP\Exceptions\ProcessException;
use Psr\Http\Message\ResponseInterface;

/**
 * @extends Command<bool>
 */
abstract class ProcessCommand extends Command {

	public function needReturnCode(): bool {
		return true;
	}

	/**
	 * @return bool
	 * @throws ProcessException
	 * @throws AuthorizationException
	 */
	#[\Override]
	public function process(ResponseInterface $response): bool {
		parent::defaultProcess($response);

		$code = $this->getResponseCode($response);
		if ($code === ResponseCode::OK) {
			return true;
		}

		throw $this->codeException($code, $response);
	}
}
