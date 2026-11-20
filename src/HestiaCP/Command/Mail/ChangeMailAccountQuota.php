<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Mail;

use neto737\HestiaCP\Command\ProcessCommand;
use neto737\HestiaCP\Exceptions\InvalidArgumentException;

class ChangeMailAccountQuota extends ProcessCommand {

	/**
	 * @param int $quota Mailbox size limit in MB, 0 for unlimited
	 * @throws InvalidArgumentException
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $account,
		private readonly int $quota
	) {
		if ($quota < 0) {
			throw new InvalidArgumentException("Invalid quota $quota, expected a size in MB or 0 for unlimited");
		}
	}

	public function getName(): string {
		return 'v-change-mail-account-quota';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->account,
			// HestiaCP's own word for no limit, as the web UI sends it
			$this->quota === 0 ? 'unlimited' : (string) $this->quota
		]);
	}
}
