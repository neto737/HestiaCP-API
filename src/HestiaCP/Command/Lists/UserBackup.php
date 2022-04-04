<?php

namespace heliocg\HestiaCP\Command\Lists;

use Nette\Utils\ArrayHash;
use heliocg\HestiaCP\Command\ListCommand;

class UserBackup extends ListCommand {

    /** @var string */
    private $user;

    /** @var string */
    private $backup;

    public function __construct(string $user, string $backup) {
        $this->user = $user;
        $this->backup = $backup;
    }

    public function getName(): string {
        return 'v-list-user-backup';
    }

    public function process(): ArrayHash {
		return $this->convertDetail(parent::process());
	}

    public function getRequestParams(): array {
        return [
            self::ARG_1 => $this->user,
            self::ARG_2 => $this->backup,
            self::ARG_3 => self::FORMAT_JSON
        ];
    }
}
