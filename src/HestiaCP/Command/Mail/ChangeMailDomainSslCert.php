<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Mail;

use neto737\HestiaCP\Command\ProcessCommand;

class ChangeMailDomainSslCert extends ProcessCommand {

	/**
	 * @param string    $sslDir Directory on the server holding <domain>.crt, <domain>.key and optionally <domain>.ca
	 * @param bool|null $restart Restart the service, or null for HestiaCP's default
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly string $sslDir,
		private readonly ?bool $restart = null
	) {}

	public function getName(): string {
		return 'v-change-mail-domain-sslcert';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->domain,
			$this->sslDir,
			$this->restart === null ? null : $this->convertBool($this->restart)
		]);
	}
}
