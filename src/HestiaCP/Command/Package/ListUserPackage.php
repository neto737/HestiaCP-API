<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Package;

use neto737\HestiaCP\Command\ListCommand;
use Psr\Http\Message\ResponseInterface;

class ListUserPackage extends ListCommand {

	public function __construct(
		private readonly string $package
	) {}

	public function getName(): string {
		return 'v-list-user-package';
	}

	#[\Override]
	public function process(ResponseInterface $response): array {
		return $this->convertDetail(parent::process($response));
	}

	public function getRequestParams(): array {
		return $this->positional([$this->package, self::FORMAT_JSON]);
	}
}
