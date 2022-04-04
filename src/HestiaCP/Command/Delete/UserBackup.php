<?php

namespace heliocg\HestiaCP\Command\Delete;

use heliocg\HestiaCP\Command\ProcessCommand;

class UserBackup extends ProcessCommand {

	/** @var string */
	private $user;

    /** @var string */
    private $backup;

	public function __construct(string $user, string $backup) {
		$this->user = $user;
        $this->backup = $backup;
	}

	public function getName(): string {
		return 'v-delete-user-backup';
	}

	public function getRequestParams(): array {
		return [
			self::ARG_1 => $this->user,
            self::ARG_2 => $this->backup
		];
	}
}
