<?php

namespace neto737\HestiaCP\Command;

use neto737\HestiaCP\Exceptions\AuthorizationException;
use neto737\HestiaCP\Exceptions\ProcessException;

abstract class ProcessCommand extends Command {

	public function needReturnCode(): bool {
		return true;
	}

	/**
	 * @return bool
	 * @throws ProcessException
	 * @throws AuthorizationException
	 */
	public function process(): bool {
		parent::defaultProcess();

		$code = $this->getResponseCode();
		if ($code === ResponseCode::OK) {
			return true;
		}

		throw $this->codeException($code);
	}
}
