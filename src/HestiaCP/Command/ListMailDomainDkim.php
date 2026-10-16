<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

class ListMailDomainDkim extends ListCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain
	) {}

	public function getName(): string {
		return 'v-list-mail-domain-dkim';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->domain, self::FORMAT_JSON]);
	}
}
