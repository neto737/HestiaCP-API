<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Cron;

use neto737\HestiaCP\Command\ListCommand;

class ListCronJob extends ListCommand {

	public function __construct(
		private readonly string $user,
		private readonly int $job
	) {}

	public function getName(): string {
		return 'v-list-cron-job';
	}

	public function process(): array {
		return $this->convertDetail(parent::process());
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, (string) $this->job, self::FORMAT_JSON]);
	}
}
