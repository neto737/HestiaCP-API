<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Dns;

use neto737\HestiaCP\Command\ListCommand;
use Psr\Http\Message\ResponseInterface;

class ListDnssecPublicKey extends ListCommand {

	public function __construct(
		private readonly string $user,
		private readonly string $domain
	) {}

	public function getName(): string {
		return 'v-list-dnssec-public-key';
	}

	#[\Override]
	public function process(ResponseInterface $response): array {
		return $this->convertDetail(parent::process($response));
	}

	public function getRequestParams(): array {
		return $this->positional([$this->user, $this->domain, self::FORMAT_JSON]);
	}
}
