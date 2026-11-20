<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Mail;

use neto737\HestiaCP\Command\ProcessCommand;

class AddMailDomainDkim extends ProcessCommand {

	/**
	 * @param int|null    $dkimSize     DKIM key size in bits (HestiaCP's default: 2048)
	 * @param string|null $dkimSelector DKIM selector (HestiaCP's default: mail)
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly ?int $dkimSize = null,
		private readonly ?string $dkimSelector = null
	) {}

	public function getName(): string {
		return 'v-add-mail-domain-dkim';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->dkimSize === null ? null : (string) $this->dkimSize,
			$this->dkimSelector
		]);
	}
}
