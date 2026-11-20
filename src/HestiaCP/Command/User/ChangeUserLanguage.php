<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\User;

use neto737\HestiaCP\Command\ProcessCommand;

class ChangeUserLanguage extends ProcessCommand {

	/**
	 * @param string    $language Language code, e.g. en or de
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $language
	) {}

	public function getName(): string {
		return 'v-change-user-language';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->language]);
	}
}
