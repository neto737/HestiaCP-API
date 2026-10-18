<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Dns;

use neto737\HestiaCP\Command\ListCommand;

class ListDnsDomain extends ListCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain
	) {}

	public function getName(): string {
		return 'v-list-dns-domain';
	}

	public function process(): array {
		return $this->convertDetail(parent::process());
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->domain, self::FORMAT_JSON]);
	}
}
