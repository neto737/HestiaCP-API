<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Web;

use neto737\HestiaCP\Command\ListCommand;

class ListWebTemplatesProxy extends ListCommand {

	public function getName(): string {
		return 'v-list-web-templates-proxy';
	}

	public function getRequestParams(): array {
		return $this->positional([self::FORMAT_JSON]);
	}
}
