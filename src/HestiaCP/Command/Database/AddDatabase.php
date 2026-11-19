<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command\Database;

use neto737\HestiaCP\Command\ProcessCommand;

class AddDatabase extends ProcessCommand {

	/**
	 * @param string|null $host    Database server, as listed by v-list-database-hosts. HestiaCP picks one when not set.
	 * @param string|null $charset Character set, e.g. UTF8MB4 or LATIN1 (HestiaCP's default: UTF8MB4)
	 */
	public function __construct(
		private readonly string $user,
		private readonly string $database,
		private readonly string $dbuser,
		#[\SensitiveParameter] private readonly string $dbpass,
		private readonly string $type = 'mysql',
		private readonly ?string $host = null,
		private readonly ?string $charset = null
	) {}

	public function getName(): string {
		return 'v-add-database';
	}

	public function getRequestParams(): array {
		return $this->positional([
			$this->user,
			$this->database,
			$this->dbuser,
			$this->dbpass,
			$this->type,
			// an empty host lets HestiaCP pick one, as when it isn't set
			$this->host,
			$this->charset
		]);
	}
}
