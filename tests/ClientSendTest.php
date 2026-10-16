<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use neto737\HestiaCP\Authorization\Credentials;
use neto737\HestiaCP\Authorization\Host;
use neto737\HestiaCP\Exceptions\AuthorizationException;
use neto737\HestiaCP\Client;
use neto737\HestiaCP\Exceptions\ClientException;
use neto737\HestiaCP\Command\Web\AddLetsEncryptDomain;
use neto737\HestiaCP\Command\User\AddUser;
use neto737\HestiaCP\Command\User\ListUser;
use neto737\HestiaCP\Command\User\ListUsers;
use neto737\HestiaCP\Command\ProcessCommand;
use neto737\HestiaCP\Command\ResponseCode;
use neto737\HestiaCP\Exceptions\InvalidResponseException;
use neto737\HestiaCP\Exceptions\ProcessException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Responses mimic HestiaCP's web/api/index.php: the access key endpoint sets the
 * Hestia-Exit-Code header and an HTTP error status, the legacy endpoint only echoes the code.
 *
 * @phpstan-import-type ClientOptions from Client
 * @phpstan-type HistoryEntry array{request: RequestInterface, response: ResponseInterface|null, error: mixed, options: array<array-key, mixed>}
 */
class ClientSendTest extends \PHPUnit\Framework\TestCase {

	/** @var array<array-key, HistoryEntry> */
	private array $history = [];

	/**
	 * @param list<Response|\Throwable> $responses
	 * @param ClientOptions $options
	 */
	private function createClient(array $responses, array $options = []): Client {
		$this->history = [];
		$stack = HandlerStack::create(new MockHandler($responses));
		$stack->push(Middleware::history($this->history));

		return Client::simpleFactory('https://someHost', 'ACCESS:SECRET', null, $options + ['handler' => $stack]);
	}

	private static function codeResponse(int $code, int $status = 200): Response {
		return new Response($status, ['Hestia-Exit-Code' => (string) $code], (string) $code);
	}

	public function testRequestFormat(): void {
		$client = $this->createClient([self::codeResponse(0)]);
		$client->send(new AddUser('user', 'pass', 'mail@domain.com'));

		$request = $this->history[0]['request'];
		$this->assertSame('POST', $request->getMethod());
		$this->assertSame('https://somehost:8083/api/', (string) $request->getUri());

		parse_str((string) $request->getBody(), $params);
		$this->assertSame([
			'access_key' => 'ACCESS',
			'secret_key' => 'SECRET',
			'arg1' => 'user',
			'arg2' => 'pass',
			'arg3' => 'mail@domain.com',
			'arg4' => 'default',
			'returncode' => 'yes',
			'cmd' => 'v-add-user'
		], $params);
	}

	public function testRequestFormatWithLegacyHash(): void {
		$this->history = [];
		$stack = HandlerStack::create(new MockHandler([self::codeResponse(0)]));
		$stack->push(Middleware::history($this->history));

		$host = new Host('https://someHost', Credentials::legacyHash('API_KEY'));
		$client = new Client($host, ['handler' => $stack]);
		$client->send(new AddUser('user', 'pass', 'mail@domain.com'));

		parse_str((string) $this->history[0]['request']->getBody(), $params);
		$this->assertSame([
			'hash' => 'API_KEY',
			'arg1' => 'user',
			'arg2' => 'pass',
			'arg3' => 'mail@domain.com',
			'arg4' => 'default',
			'returncode' => 'yes',
			'cmd' => 'v-add-user'
		], $params);
	}

	public function testVerifyTlsByDefault(): void {
		$client = $this->createClient([self::codeResponse(0)]);
		$client->send(new AddUser('user', 'pass', 'mail@domain.com'));
		$this->assertTrue($this->history[0]['options']['verify']);
	}

	public function testVerifyTlsCanBeDisabled(): void {
		$client = $this->createClient([self::codeResponse(0)], ['verify' => false]);
		$client->send(new AddUser('user', 'pass', 'mail@domain.com'));
		$this->assertFalse($this->history[0]['options']['verify']);
	}

	public function testCommandTimeout(): void {
		$client = $this->createClient([self::codeResponse(0), self::codeResponse(0), self::codeResponse(0)]);

		$client->send(new AddUser('user', 'pass', 'mail@domain.com'));
		$this->assertEquals(10, $this->history[0]['options']['timeout']);

		$client->send(new AddLetsEncryptDomain('user', 'domain.com'));
		$this->assertEquals(90, $this->history[1]['options']['timeout']);

		$client->send(new \neto737\HestiaCP\Command\Backup\BackupUser('user'));
		$this->assertEquals(0, $this->history[2]['options']['timeout']);
	}

	public function testCommandTimeoutNeverShortensClientTimeout(): void {
		$client = $this->createClient([self::codeResponse(0)], ['timeout' => 120]);
		$client->send(new AddLetsEncryptDomain('user', 'domain.com'));
		$this->assertEquals(120, $this->history[0]['options']['timeout']);
	}

	public function testProcessSuccess(): void {
		$client = $this->createClient([self::codeResponse(0)]);
		$this->assertTrue($client->send(new AddUser('user', 'pass', 'mail@domain.com')));
	}

	public function testProcessSuccessLegacy(): void {
		$client = $this->createClient([new Response(200, [], '0')]);
		$this->assertTrue($client->send(new AddUser('user', 'pass', 'mail@domain.com')));
	}

	public function testProcessError(): void {
		$client = $this->createClient([self::codeResponse(ResponseCode::E_EXISTS, 400)]);

		try {
			$client->send(new AddUser('user', 'pass', 'mail@domain.com'));
			$this->fail('ProcessException expected');
		} catch (ProcessException $e) {
			$this->assertNotInstanceOf(AuthorizationException::class, $e);
			$this->assertSame(ResponseCode::E_EXISTS, $e->getCode());
			$this->assertSame('Object already exists', $e->getMessage());
		}
	}

	public function testProcessErrorLegacy(): void {
		$client = $this->createClient([new Response(200, [], '3')]);

		$this->expectException(ProcessException::class);
		$this->expectExceptionCode(ResponseCode::E_NOTEXIST);
		$client->send(new AddUser('user', 'pass', 'mail@domain.com'));
	}

	public function testUnknownErrorCode(): void {
		$client = $this->createClient([self::codeResponse(99, 400)]);

		$this->expectException(ProcessException::class);
		$this->expectExceptionMessage('Unknown error (exit code 99)');
		$client->send(new AddUser('user', 'pass', 'mail@domain.com'));
	}

	public function testAuthorizationFailure(): void {
		$client = $this->createClient([self::codeResponse(ResponseCode::E_PASSWORD, 401)]);

		$this->expectException(AuthorizationException::class);
		$this->expectExceptionCode(ResponseCode::E_PASSWORD);
		$client->send(new AddUser('user', 'pass', 'mail@domain.com'));
	}

	public function testAuthorizationFailureLegacyText(): void {
		$client = $this->createClient([
			new Response(401, ['Hestia-Exit-Code' => '9'], 'Error: authentication failed')
		]);

		$this->expectException(AuthorizationException::class);
		$client->send(new ListUsers());
	}

	public function testListErrorKeepsHestiaMessage(): void {
		$client = $this->createClient([
			new Response(400, ['Hestia-Exit-Code' => '3'], "Error: user someone doesn't exist")
		]);

		$this->expectException(ProcessException::class);
		$this->expectExceptionMessage("Object doesn't exist (Error: user someone doesn't exist)");
		$client->send(new ListUser('someone'));
	}

	public function testUnexpectedHttpStatus(): void {
		$client = $this->createClient([new Response(502, [], '<html>Bad Gateway</html>')]);

		$this->expectException(InvalidResponseException::class);
		$client->send(new ListUsers());
	}

	public function testNotHestiaResponse(): void {
		$client = $this->createClient([new Response(200, [], '<html>Hello</html>')]);

		$this->expectException(InvalidResponseException::class);
		$client->send(new AddUser('user', 'pass', 'mail@domain.com'));
	}

	public function testConnectionError(): void {
		$client = $this->createClient([new ConnectException('Connection refused', new Request('POST', 'test'))]);

		try {
			$client->send(new ListUsers());
			$this->fail('ClientException expected');
		} catch (ClientException $e) {
			$this->assertInstanceOf(ConnectException::class, $e->getPrevious());
		}
	}

	// a custom command that fails with its own exception is wrapped, keeping the original
	public function testCommandProcessingError(): void {
		$client = $this->createClient([self::codeResponse(0)]);
		$command = new class () extends ProcessCommand {
			public function getName(): string {
				return 'v-custom';
			}

			public function getRequestParams(): array {
				return [];
			}

			public function process(): bool {
				throw new \RuntimeException('Something broke');
			}
		};

		try {
			$client->send($command);
			$this->fail('ClientException expected');
		} catch (ClientException $e) {
			$this->assertSame('Fatal processing error (Something broke)', $e->getMessage());
			$this->assertInstanceOf(\RuntimeException::class, $e->getPrevious());
		}
	}

	public function testList(): void {
		$client = $this->createClient([
			new Response(200, ['Hestia-Exit-Code' => '0'], '{"admin": {"NAME": "System Administrator"}, "user": {"NAME": "User"}}')
		]);

		$users = $client->send(new ListUsers());
		$this->assertSame([
			'admin' => ['NAME' => 'System Administrator'],
			'user' => ['NAME' => 'User']
		], $users);
	}

	public function testListDetail(): void {
		$client = $this->createClient([new Response(200, [], '{"admin": {"NAME": "System Administrator"}}')]);

		$user = $client->send(new ListUser('admin'));
		$this->assertSame(['NAME' => 'System Administrator'], $user);
	}

	public function testListDetailNotObject(): void {
		$client = $this->createClient([new Response(200, [], '{"admin": "System Administrator"}')]);

		$this->expectException(InvalidResponseException::class);
		$client->send(new ListUser('admin'));
	}

	public function testListNotList(): void {
		$client = $this->createClient([new Response(200, [], '"OK"')]);

		$this->expectException(ProcessException::class);
		$client->send(new ListUsers());
	}

	public function testListEmpty(): void {
		$client = $this->createClient([new Response(204, ['Hestia-Exit-Code' => '0'], '')]);
		$this->assertSame([], $client->send(new ListUsers()));
	}

	public function testListInvalidJson(): void {
		$client = $this->createClient([new Response(200, [], 'not json')]);

		$this->expectException(ProcessException::class);
		$this->expectExceptionMessage('not json');
		$client->send(new ListUsers());
	}

	public function testListDatabasesReturnsAll(): void {
		$client = $this->createClient([
			new Response(200, [], '{"admin_one": {"DBUSER": "admin_one"}, "admin_two": {"DBUSER": "admin_two"}}')
		]);

		$databases = $client->getModuleDatabase('admin')->listDatabases();
		$this->assertSame(['admin_one', 'admin_two'], array_keys($databases));
	}

	// output of bin/v-list-mail-domain-dkim in HestiaCP 1.10.5, PEM newlines escaped by the script
	public function testListDomainDkim(): void {
		$body = <<<'JSON'
{
	"domain.com": {
        "PEM": "-----BEGIN PRIVATE KEY-----\nMIIEvQ\n-----END PRIVATE KEY-----\n",
        "PUB": "-----BEGIN PUBLIC KEY-----\nMIIBIj\n-----END PUBLIC KEY-----\n"
	}
}
JSON;
		$client = $this->createClient([new Response(200, [], $body)]);

		$dkim = $client->getModuleMail('admin')->listDomainDkim('domain.com');
		$this->assertSame("-----BEGIN PRIVATE KEY-----\nMIIEvQ\n-----END PRIVATE KEY-----\n", $dkim['domain.com']['PEM']);
		$this->assertSame("-----BEGIN PUBLIC KEY-----\nMIIBIj\n-----END PUBLIC KEY-----\n", $dkim['domain.com']['PUB']);
	}

	// output of bin/v-list-mail-domain-dkim-dns in HestiaCP 1.10.5
	public function testListDomainDkimDns(): void {
		$body = <<<'JSON'
{

	"mail._domainkey": {
            "TTL": "3600",
            "TXT": "'v=DKIM1; k=rsa; p=MIIBIj'"
	}
}
JSON;
		$client = $this->createClient([new Response(200, [], $body)]);

		$this->assertSame(
			['mail._domainkey' => ['TTL' => '3600', 'TXT' => "'v=DKIM1; k=rsa; p=MIIBIj'"]],
			$client->getModuleMail('admin')->listDomainDkimDns('domain.com')
		);
	}

	public function testBackup(): void {
		$client = $this->createClient([self::codeResponse(0), self::codeResponse(0)]);

		$this->assertTrue($client->getModuleBackup('admin')->backup());
		$this->assertTrue($client->getModuleUser()->backup());

		parse_str((string) $this->history[1]['request']->getBody(), $params);
		$this->assertSame('v-backup-users', $params['cmd']);
		$this->assertArrayNotHasKey('arg1', $params);
	}

	public function testAuthorizationValid(): void {
		$client = $this->createClient([self::codeResponse(0)]);
		$this->assertTrue($client->testAuthorization());

		parse_str((string) $this->history[0]['request']->getBody(), $params);
		$this->assertSame('v-list-sys-info', $params['cmd']);
	}

	public function testAuthorizationValidLegacy(): void {
		$client = $this->createClient([new Response(200, [], '0')]);
		$this->assertTrue($client->testAuthorization());
	}

	public function testAuthorizationInvalid(): void {
		$client = $this->createClient([
			self::codeResponse(ResponseCode::E_PASSWORD, 401),
			self::codeResponse(ResponseCode::E_FORBIDDEN, 401)
		]);

		$this->assertFalse($client->testAuthorization());
		$this->assertFalse($client->testAuthorization());
	}

	public function testAuthorizationInvalidLegacy(): void {
		$client = $this->createClient([
			new Response(200, [], (string) ResponseCode::E_PASSWORD),
			new Response(200, [], 'Error: authentication failed')
		]);

		$this->assertFalse($client->testAuthorization());
		$this->assertFalse($client->testAuthorization());
	}

	public function testAuthorizationErrorLegacy(): void {
		$client = $this->createClient([new Response(200, [], (string) ResponseCode::E_DISABLED)]);

		try {
			$client->testAuthorization();
			$this->fail('ProcessException expected');
		} catch (ProcessException $e) {
			$this->assertNotInstanceOf(AuthorizationException::class, $e);
			$this->assertSame(ResponseCode::E_DISABLED, $e->getCode());
		}
	}

	public function testAuthorizationApiDisabled(): void {
		$client = $this->createClient([self::codeResponse(ResponseCode::E_DISABLED, 400)]);

		$this->expectException(ProcessException::class);
		$this->expectExceptionCode(ResponseCode::E_DISABLED);
		$client->testAuthorization();
	}

	public function testAuthorizationNotHestia(): void {
		$client = $this->createClient([new Response(200, [], '<html>Hello</html>')]);

		$this->expectException(InvalidResponseException::class);
		$client->testAuthorization();
	}
}
