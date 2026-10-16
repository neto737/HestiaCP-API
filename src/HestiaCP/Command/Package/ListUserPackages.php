<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Package;

use neto737\HestiaCP\Command\ListCommand;

class ListUserPackages extends ListCommand {

	public function getName(): string {
		return 'v-list-user-packages';
	}

	public function getRequestParams(): array {
		return $this->positional([self::FORMAT_JSON]);
	}
}
