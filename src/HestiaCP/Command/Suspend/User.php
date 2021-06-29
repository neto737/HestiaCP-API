<?php

namespace neto737\HestiaCP\Command\Suspend;

use neto737\HestiaCP\Command\ProcessCommand;

class User extends ProcessCommand {

    /** @var string */
    private $user;

    /** @var string */
    private $restart;

    public function __construct(string $user, bool $restart = null) {
        $this->user = $user;
        $this->restart = $restart;
    }

    public function getName(): string {
        return 'v-suspend-user';
    }

    public function getRequestParams(): array {
        return [
            self::ARG_1 => $this->user,
            self::ARG_2 => $this->convertBool($this->restart)
        ];
    }
}
