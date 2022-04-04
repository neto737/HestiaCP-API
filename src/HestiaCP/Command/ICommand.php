<?php

namespace heliocg\HestiaCP\Command;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

interface ICommand {

	public function setLastResponse(ResponseInterface $response): void;

	public function getName(): string;

	public function needReturnCode(): bool;

	public function process();

	public function processException(ClientException $exception);

	public function getRequestParams(): array;
}
