<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Mail;

use neto737\HestiaCP\Command\ProcessCommand;

class AddMailDomain extends ProcessCommand {

	/**
	 * The optional arguments are only sent when set, so HestiaCP keeps its own defaults.
	 *
	 * @param int|null    $dkimSize     DKIM key size in bits (HestiaCP's default: 2048)
	 * @param bool|null   $restart      Restart the web and proxy servers (HestiaCP's default: right away, or queued)
	 * @param bool|null   $rejectSpam   Reject messages marked as spam instead of delivering them (HestiaCP's default: no)
	 * @param string|null $dkimSelector DKIM selector (HestiaCP's default: mail)
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $domain,
		private readonly bool $antispam = true,
		private readonly bool $antivirus = true,
		private readonly bool $dkim = true,
		private readonly ?int $dkimSize = null,
		private readonly ?bool $restart = null,
		private readonly ?bool $rejectSpam = null,
		private readonly ?string $dkimSelector = null
	) {}

	public function getName(): string {
		return 'v-add-mail-domain';
	}

	public function getRequestParams(): array {
		$rejectSpam = $this->rejectSpam;
		// HestiaCP stores an empty REJECT_SPAM argument as is instead of its default "no"
		if ($rejectSpam === null && $this->dkimSelector !== null) {
			$rejectSpam = false;
		}

		return $this->positional([
			$this->user,
			$this->domain,
			$this->convertBool($this->antispam),
			$this->convertBool($this->antivirus),
			$this->convertBool($this->dkim),
			$this->dkimSize === null ? null : (string) $this->dkimSize,
			$this->restart === null ? null : $this->convertBool($this->restart),
			$rejectSpam === null ? null : $this->convertBool($rejectSpam),
			$this->dkimSelector
		]);
	}
}
