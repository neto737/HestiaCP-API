<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Dns;

use neto737\HestiaCP\Command\ListCommand;

class ListDnsTemplates extends ListCommand {

	public function getName(): string {
		return 'v-list-dns-templates';
	}

	public function getRequestParams(): array {
		return $this->positional([self::FORMAT_JSON]);
	}
}
