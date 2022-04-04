<?php

namespace heliocg\HestiaCP\Command;

use GuzzleHttp\Exception\ClientException;
use heliocg\HestiaCP\AuthorizationException;
use heliocg\HestiaCP\InvalidResponseException;
use heliocg\HestiaCP\ProcessException;
use Psr\Http\Message\ResponseInterface;

abstract class Command implements ICommand {

	protected const ARG_1 = 'arg1';
	protected const ARG_2 = 'arg2';
	protected const ARG_3 = 'arg3';
	protected const ARG_4 = 'arg4';
	protected const ARG_5 = 'arg5';
	protected const ARG_6 = 'arg6';

	protected const FORMAT_JSON = 'json';

	private $lastResponse;

	/**
	 * @param ClientException $exception
	 * @throws ProcessException
	 */
	public function processException(ClientException $exception): void {
		throw new ProcessException($exception->getMessage());
	}

	public function setLastResponse(ResponseInterface $response): void {
		$this->lastResponse = $response->getBody()->getContents();
	}

	public function getResponseText(): ?string {
		return $this->lastResponse;
	}

	/**
	 * @return int
	 * @throws InvalidResponseException
	 */
	public function getResponseCode(): int {
		$responseText = $this->getResponseText();

		if (!preg_match('~^[\d]+$~', $responseText)) {
			throw new InvalidResponseException('Response is not code. Is hostname really HestiaCP?');
		}

		return (int) $responseText;
	}

	/**
	 * @throws AuthorizationException
	 */
	public function defaultProcess(): void {
		$responseText = $this->getResponseText();

		if ($responseText === 'Error: authentication failed') {
			throw new AuthorizationException('Authorization failed! Bad user or password');
		}
	}

	/**
	 * @param $code
	 * @throws ProcessException
	 */
	protected function throwCodeException($code): void {
		throw new ProcessException(ResponseCode::$messages[$code]);
	}

	/**
	 * @param bool $bool
	 * @return string
	 */
	protected function convertBool(bool $bool): string {
		return $bool === true ? 'yes' : 'no';
	}
}
