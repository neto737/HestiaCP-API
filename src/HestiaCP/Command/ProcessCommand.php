<?php

namespace heliocg\HestiaCP\Command;

use heliocg\HestiaCP\AuthorizationException;
use heliocg\HestiaCP\Command\Command;
use heliocg\HestiaCP\ProcessException;

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
