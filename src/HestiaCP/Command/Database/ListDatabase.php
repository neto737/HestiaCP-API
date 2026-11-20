<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Database;

use neto737\HestiaCP\Command\ListCommand;
use Psr\Http\Message\ResponseInterface;

class ListDatabase extends ListCommand {

	public function __construct(
		private readonly string $username,
		private readonly string $database
	) {}

	public function getName(): string {
		return 'v-list-database';
	}

	#[\Override]
	public function process(ResponseInterface $response): array {
		return $this->convertDetail(parent::process($response));
	}

	public function getRequestParams(): array {
		return $this->positional([$this->username, $this->database, self::FORMAT_JSON]);
	}
}
