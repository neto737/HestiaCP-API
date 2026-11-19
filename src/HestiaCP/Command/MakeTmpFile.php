<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

use neto737\HestiaCP\Exceptions\AuthorizationException;
use neto737\HestiaCP\Exceptions\InvalidArgumentException;
use neto737\HestiaCP\Exceptions\InvalidResponseException;
use neto737\HestiaCP\Exceptions\ProcessException;
use Psr\Http\Message\ResponseInterface;

/**
 * Writes a file to /tmp on the server, for the scripts that read their input from a file.
 *
 * v-make-tmp-file isn't a script: HestiaCP's API handles it itself. It creates /tmp/<name> with
 * mode 0600, replacing an existing file, and doesn't delete it afterwards.
 *
 * @extends Command<bool>
 */
class MakeTmpFile extends Command {

	/**
	 * @param string $name    File name, without a directory
	 * @param string $content File content. HestiaCP trims it and adds a newline.
	 * @throws InvalidArgumentException
	 */
	public function __construct(
		private readonly string $name,
		private readonly string $content
	) {
		if ($name === '' || $name === '.' || $name === '..' || basename($name) !== $name || str_contains($name, '\\')) {
			throw new InvalidArgumentException("Invalid temporary file name '$name', expected a file name without a directory");
		}
	}

	public function getName(): string {
		return 'v-make-tmp-file';
	}

	/**
	 * The legacy endpoint answers returncode=yes with an empty body, so the command is sent without it.
	 */
	public function needReturnCode(): bool {
		return false;
	}

	public function getRequestParams(): array {
		return $this->positional([$this->content, $this->name]);
	}

	/**
	 * The path of the file on the server, to pass to the script that reads it.
	 */
	public function getPath(): string {
		return '/tmp/' . $this->name;
	}

	/**
	 * @throws AuthorizationException
	 * @throws ProcessException
	 * @throws InvalidResponseException
	 */
	#[\Override]
	public function process(ResponseInterface $response): bool {
		$this->defaultProcess($response);

		// an access key gets exit code 0 in the header, the legacy endpoint answers "OK"
		if ($this->getExitCode($response) === ResponseCode::OK || trim((string) $response->getBody()) === 'OK') {
			return true;
		}

		throw new InvalidResponseException('Unexpected response to v-make-tmp-file. Is hostname really HestiaCP?');
	}
}
