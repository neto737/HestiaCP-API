<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

/**
 * Runs any HestiaCP script that changes something, for scripts with no dedicated command class.
 * For a script that prints JSON, use {@see RawListCommand}.
 *
 * new RawCommand('v-add-firewall-rule', ['DROP', '203.0.113.7', '25'])
 */
class RawCommand extends ProcessCommand {

	use RawArguments;
}
