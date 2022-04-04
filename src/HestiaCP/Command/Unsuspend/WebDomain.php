<?php

namespace heliocg\HestiaCP\Command\Unsuspend;

use heliocg\HestiaCP\Command\ProcessCommand;

class WebDomain extends ProcessCommand {

    /** @var string */
    private $user;

    /** @var string */
    private $domain;

    /** @var string */
    private $restart;

    public function __construct(string $user, string $domain, bool $restart = false) {
        $this->user = $user;
        $this->domain = $domain;
        $this->restart = $restart;
    }

    public function getName(): string {
        return 'v-unsuspend-web-domain';
    }

    public function getRequestParams(): array {
        return [
            self::ARG_1 => $this->user,
            self::ARG_2 => $this->domain,
            self::ARG_3 => $this->convertBool($this->restart)
        ];
    }
}
