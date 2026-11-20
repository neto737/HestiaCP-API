<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Module;

use neto737\HestiaCP\Command\Package\ListUserPackage;
use neto737\HestiaCP\Command\Package\ListUserPackages;
use neto737\HestiaCP\Command\Package\AddUserPackage;
use neto737\HestiaCP\Command\Package\CopyUserPackage;
use neto737\HestiaCP\Command\Package\DeleteUserPackage;
use neto737\HestiaCP\Command\Package\RenameUserPackage;
use neto737\HestiaCP\Command\MakeTmpFile;
use neto737\HestiaCP\Exceptions\InvalidArgumentException;

class Packages extends Module {

	/**
	 * This function obtains the list of all hosting packages.
	 *
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function list(): array {
		return $this->client->send(new ListUserPackages());
	}

	/**
	 * This function obtains the parameters of a hosting package.
	 *
	 * @param string $package
	 * @return array<mixed>
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function detail(string $package): array {
		return $this->client->send(new ListUserPackage($package));
	}

	/**
	 * This function adds a hosting package, or replaces one when $rewrite is true.
	 *
	 * HestiaCP needs every setting, so start from an existing package:
	 * add('gold', ['DISK_QUOTA' => '10240'] + $packages->detail('default'))
	 *
	 * It sends two requests: v-make-tmp-file writes the settings to /tmp on the server, where
	 * v-add-user-package reads them.
	 *
	 * @param string                    $package
	 * @param array<string, int|string> $settings e.g. ['WEB_DOMAINS' => 5, 'DISK_QUOTA' => 'unlimited', ...]
	 * @param bool                      $rewrite  Replace the package if it exists
	 * @return bool
	 * @throws InvalidArgumentException
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function add(string $package, array $settings, bool $rewrite = false): bool {
		$lines = [];
		foreach ($settings as $key => $value) {
			$value = (string) $value;
			// HestiaCP reads the file as KEY='value' lines
			if (!preg_match('~^[A-Z][A-Z0-9_]*$~', (string) $key) || preg_match("~['\\r\\n]~", $value)) {
				throw new InvalidArgumentException("Invalid package setting '$key', expected an upper-case name and a value without quotes or line breaks");
			}
			$lines[] = $key . "='" . $value . "'";
		}

		$file = new MakeTmpFile('hestiacp-api-package-' . $package, implode("\n", $lines));
		$this->client->send($file);
		return $this->client->send(new AddUserPackage($file->getPath(), $package, $rewrite ? true : null));
	}

	/**
	 * This function copies a hosting package under a new name.
	 *
	 * @param string $package
	 * @param string $newPackage
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function copy(string $package, string $newPackage): bool {
		return $this->client->send(new CopyUserPackage($package, $newPackage));
	}

	/**
	 * This function renames a hosting package.
	 *
	 * As of HestiaCP 1.10.9, the users that have the package keep its old name, which then no longer exists.
	 * To rename a package that users have, copy() it, changePackage() each user, and delete() the old one.
	 *
	 * @param string $package
	 * @param string $newPackage
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function rename(string $package, string $newPackage): bool {
		return $this->client->send(new RenameUserPackage($package, $newPackage));
	}

	/**
	 * This function deletes a hosting package. HestiaCP moves the users that have it to the default package,
	 * and, as of HestiaCP 1.10.9, also the users of every package whose name starts with it: deleting "gold"
	 * moves the users of "gold-plus" too.
	 *
	 * @param string $package
	 * @return bool
	 * @throws \neto737\HestiaCP\Exceptions\ClientException
	 * @throws \neto737\HestiaCP\Exceptions\ProcessException
	 */
	public function delete(string $package): bool {
		return $this->client->send(new DeleteUserPackage($package));
	}
}
