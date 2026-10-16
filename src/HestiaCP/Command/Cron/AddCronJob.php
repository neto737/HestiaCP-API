<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Cron;

use neto737\HestiaCP\Command\ProcessCommand;

class AddCronJob extends ProcessCommand {

	/**
	 * @param int|null  $job     Job ID, or null for the next free ID
	 * @param bool|null $restart Restart the cron service, or null for HestiaCP's default
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $min,
		private readonly string $hour,
		private readonly string $day,
		private readonly string $month,
		private readonly string $wday,
		private readonly string $command,
		private readonly ?int $job = null,
		private readonly ?bool $restart = null
	) {}

	public function getName(): string {
		return 'v-add-cron-job';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->min,
			$this->hour,
			$this->day,
			$this->month,
			$this->wday,
			$this->command,
			$this->job === null ? null : (string) $this->job,
			$this->restart === null ? null : $this->convertBool($this->restart)
		]);
	}
}
