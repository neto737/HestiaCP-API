<?php

namespace neto737\HestiaCP\Command\Backup;

use neto737\HestiaCP\Command\ListCommand;

class User extends ListCommand {

    /** @var string */
    private $user;

    /** @var bool */
    private $notify;

    public function __construct(string $user, bool $notify = true) {
        $this->user = $user;
        $this->notify = $notify;
    }

    public function getName(): string {
        return 'v-backup-user';
    }

    public function getRequestParams(): array {
        return [
            self::ARG_1 => $this->user,
            self::ARG_2 => $this->convertBool($this->notify)
        ];
    }
}
