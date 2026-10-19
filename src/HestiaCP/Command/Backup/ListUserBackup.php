<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Backup;

use neto737\HestiaCP\Command\ListCommand;
use Psr\Http\Message\ResponseInterface;

class ListUserBackup extends ListCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $backup
	) {}

	public function getName(): string {
		return 'v-list-user-backup';
	}

	public function process(ResponseInterface $response): array {
		return $this->convertDetail(parent::process($response));
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->backup, self::FORMAT_JSON]);
	}
}
