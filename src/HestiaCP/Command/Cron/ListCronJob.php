<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Cron;

use neto737\HestiaCP\Command\ListCommand;
use Psr\Http\Message\ResponseInterface;

class ListCronJob extends ListCommand {

	public function __construct(
		private readonly string $user,
		private readonly int $job
	) {}

	public function getName(): string {
		return 'v-list-cron-job';
	}

	#[\Override]
	public function process(ResponseInterface $response): array {
		return $this->convertDetail(parent::process($response));
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, (string) $this->job, self::FORMAT_JSON]);
	}
}
