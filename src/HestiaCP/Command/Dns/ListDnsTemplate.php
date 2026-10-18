<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Dns;

use neto737\HestiaCP\Command\ListCommand;

class ListDnsTemplate extends ListCommand {

	public function __construct(
		private readonly string $template
	) {}

	public function getName(): string {
		return 'v-list-dns-template';
	}

	public function getRequestParams(): array {
		return $this->positional([$this->template, self::FORMAT_JSON]);
	}
}
