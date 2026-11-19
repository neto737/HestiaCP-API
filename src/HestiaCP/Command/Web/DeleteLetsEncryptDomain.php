<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Web;

use neto737\HestiaCP\Command\ProcessCommand;

class DeleteLetsEncryptDomain extends ProcessCommand {

	/**
	 * @param bool $mail Delete the mail domain's certificate (mail.DOMAIN) instead of the web domain's
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly bool $restart = false,
		private readonly bool $mail = false
	) {}

	public function getName(): string {
		return 'v-delete-letsencrypt-domain';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->convertBool($this->restart),
			// HestiaCP switches to the mail domain for any non-empty value, "no" included
			$this->mail ? 'yes' : null
		]);
	}
}
