<?php

namespace neto737\HestiaCP\Command\Unsuspend;

use neto737\HestiaCP\Command\ProcessCommand;

class User extends ProcessCommand {

    /** @var string */
    private $user;

    /** @var string */
    private $restart;

    public function __construct(string $user, bool $restart = false) {
        $this->user = $user;
        $this->restart = $restart;
    }

    public function getName(): string {
        return 'v-unsuspend-user';
    }

    public function getRequestParams(): array {
        return [
            self::ARG_1 => $this->user,
            self::ARG_2 => $this->convertBool($this->restart)
        ];
    }
}
