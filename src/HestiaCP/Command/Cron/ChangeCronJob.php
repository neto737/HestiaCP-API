<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Cron;

use neto737\HestiaCP\Command\ProcessCommand;

class ChangeCronJob extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly int $job,
		private readonly string $min,
		private readonly string $hour,
		private readonly string $day,
		private readonly string $month,
		private readonly string $wday,
		private readonly string $command
	) {}

	public function getName(): string {
		return 'v-change-cron-job';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			(string) $this->job,
			$this->min,
			$this->hour,
			$this->day,
			$this->month,
			$this->wday,
			$this->command
		]);
	}
}
