<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Package;

use neto737\HestiaCP\Command\ProcessCommand;

/**
 * Adds a hosting package from a file on the server that holds its settings, one KEY='value' per line,
 * such as the one {@see \neto737\HestiaCP\Command\MakeTmpFile} writes. Packages::add() does both.
 */
class AddUserPackage extends ProcessCommand {

	/**
	 * @param string    $file    Path of the settings file on the server
	 * @param bool|null $rewrite Replace the package if it exists, or null for HestiaCP's default (no)
	 */
	public function __construct(
		private readonly string $file,
		private readonly string $package,
		private readonly ?bool $rewrite = null
	) {}

	public function getName(): string {
		return 'v-add-user-package';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->file,
			$this->package,
			$this->rewrite === null ? null : $this->convertBool($this->rewrite)
		]);
	}
}
