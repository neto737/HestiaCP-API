<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Module;

use neto737\HestiaCP\Client;
use neto737\HestiaCP\Command\Cron\AddCronJob;
use neto737\HestiaCP\Command\Cron\ChangeCronJob;
use neto737\HestiaCP\Command\Cron\DeleteCronJob;
use neto737\HestiaCP\Command\Cron\ListCronJob;
use neto737\HestiaCP\Command\Cron\ListCronJobs;
use neto737\HestiaCP\Command\Cron\SuspendCronJob;
use neto737\HestiaCP\Command\Cron\UnsuspendCronJob;

class Crons extends Module {

	private string $user;

	public function __construct(Client $client, string $user) {
		parent::__construct($client);
		$this->user = $user;
	}

	/**
	 * This function obtains the list of all user's cron jobs, keyed by job ID.
	 *
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listJobs(): array {
		return $this->client->send(new ListCronJobs($this->user));
	}

	/**
	 * This function obtains the parameters of a cron job.
	 *
	 * @param int $job
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function listJob(int $job): array {
		return $this->client->send(new ListCronJob($this->user, $job));
	}

	/**
	 * This function adds a new cron job.
	 *
	 * @param string    $min
	 * @param string    $hour
	 * @param string    $day
	 * @param string    $month
	 * @param string    $wday
	 * @param string    $command
	 * @param int|null  $job     Job ID, or null for the next free ID
	 * @param bool|null $restart Restart the cron service, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function addJob(string $min, string $hour, string $day, string $month, string $wday, string $command, ?int $job = null, ?bool $restart = null): bool {
		return $this->client->send(new AddCronJob($this->user, $min, $hour, $day, $month, $wday, $command, $job, $restart));
	}

	/**
	 * This function changes the schedule and command of a cron job.
	 *
	 * @param int    $job
	 * @param string $min
	 * @param string $hour
	 * @param string $day
	 * @param string $month
	 * @param string $wday
	 * @param string $command
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function changeJob(int $job, string $min, string $hour, string $day, string $month, string $wday, string $command): bool {
		return $this->client->send(new ChangeCronJob($this->user, $job, $min, $hour, $day, $month, $wday, $command));
	}

	/**
	 * This function suspends a cron job.
	 *
	 * @param int       $job
	 * @param bool|null $restart Restart the cron service, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function suspendJob(int $job, ?bool $restart = null): bool {
		return $this->client->send(new SuspendCronJob($this->user, $job, $restart));
	}

	/**
	 * This function unsuspends a cron job.
	 *
	 * @param int       $job
	 * @param bool|null $restart Restart the cron service, or null for HestiaCP's default (restart)
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function unsuspendJob(int $job, ?bool $restart = null): bool {
		return $this->client->send(new UnsuspendCronJob($this->user, $job, $restart));
	}

	/**
	 * This function deletes a cron job.
	 *
	 * @param int       $job
	 * @param bool|null $restart Restart the cron service, or null for HestiaCP's default
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function deleteJob(int $job, ?bool $restart = null): bool {
		return $this->client->send(new DeleteCronJob($this->user, $job, $restart));
	}
}
