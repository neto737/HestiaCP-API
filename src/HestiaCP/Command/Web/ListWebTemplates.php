<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Web;

use neto737\HestiaCP\Command\ListCommand;

class ListWebTemplates extends ListCommand {

	public function getName(): string {
		return 'v-list-web-templates';
	}

	public function getRequestParams(): array {
		return $this->positional([self::FORMAT_JSON]);
	}
}
