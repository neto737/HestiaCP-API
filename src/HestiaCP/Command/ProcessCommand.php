<?php

namespace neto737\HestiaCP\Command;

use neto737\HestiaCP\AuthorizationException;
use neto737\HestiaCP\Command\Command;
use neto737\HestiaCP\ProcessException;

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

		if ($this->getResponseCode() === 0) {
			return true;
		}

		$this->throwCodeException($this->getResponseCode());
	}
}
