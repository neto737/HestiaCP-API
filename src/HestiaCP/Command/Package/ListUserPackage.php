<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Package;

use neto737\HestiaCP\Command\ListCommand;

class ListUserPackage extends ListCommand {

	public function __construct(
		private readonly string $package
	) {}

	public function getName(): string {
		return 'v-list-user-package';
	}

	public function process(): array {
		return $this->convertDetail(parent::process());
	}

	public function getRequestParams(): array {
		return $this->positional([$this->package, self::FORMAT_JSON]);
	}
}
