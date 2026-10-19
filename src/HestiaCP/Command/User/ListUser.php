<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\User;

use neto737\HestiaCP\Command\ListCommand;
use Psr\Http\Message\ResponseInterface;

class ListUser extends ListCommand {

	public function __construct(
		private readonly string $username
	) {}

	public function getName(): string {
		return 'v-list-user';
	}

	public function process(ResponseInterface $response): array {
		return $this->convertDetail(parent::process($response));
	}

	public function getRequestParams(): array {
		return $this->positional([$this->username, self::FORMAT_JSON]);
	}
}
