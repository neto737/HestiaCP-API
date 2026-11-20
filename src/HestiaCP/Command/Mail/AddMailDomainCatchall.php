<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Mail;

use neto737\HestiaCP\Command\ProcessCommand;

class AddMailDomainCatchall extends ProcessCommand {

	/**
	 * @param string    $email Address that receives mail sent to unknown accounts
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $email
	) {}

	public function getName(): string {
		return 'v-add-mail-domain-catchall';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->domain, $this->email]);
	}
}
