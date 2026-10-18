<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Dns;

use neto737\HestiaCP\Command\ListCommand;

class ListDnsDomains extends ListCommand {

	public function __construct(
		private readonly string $user
	) {}

	public function getName(): string {
		return 'v-list-dns-domains';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, self::FORMAT_JSON]);
	}
}
