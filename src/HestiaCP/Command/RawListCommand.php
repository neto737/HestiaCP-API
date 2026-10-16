<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

/**
 * Runs any HestiaCP script that prints JSON, for scripts with no dedicated command class.
 * Most list scripts print JSON only when `json` is passed as their format argument, so include it in the arguments.
 *
 * new RawListCommand('v-list-cron-jobs', ['admin', 'json'])
 */
class RawListCommand extends ListCommand {

	use RawArguments;
}
