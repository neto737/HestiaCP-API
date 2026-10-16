<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

class DeleteWebDomainFtp extends ProcessCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $ftpUser
	) {}

	public function getName(): string {
		return 'v-delete-web-domain-ftp';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->domain, $this->ftpUser]);
	}
}
