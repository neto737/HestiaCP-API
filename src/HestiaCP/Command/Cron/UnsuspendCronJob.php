<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Cron;

use neto737\HestiaCP\Command\ProcessCommand;

class UnsuspendCronJob extends ProcessCommand {

	/**
	 * @param bool|null $restart Restart the cron service, or null for HestiaCP's default (restart)
	 */
	public function __construct(
		private readonly string $user,
		private readonly int $job,
		private readonly ?bool $restart = null
	) {}

	public function getName(): string {
		return 'v-unsuspend-cron-job';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			(string) $this->job,
			$this->restart === null ? null : $this->convertBool($this->restart)
		]);
	}
}
