<?php

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Nette\Utils\ArrayHash;
use neto737\HestiaCP\AuthorizationException;
use neto737\HestiaCP\Client;
use neto737\HestiaCP\ClientException;
use neto737\HestiaCP\Command\Add\LetsEncryptDomain;
use neto737\HestiaCP\Command\Add\User as AddUser;
use neto737\HestiaCP\Command\Lists\Databases;
use neto737\HestiaCP\Command\Lists\User;
use neto737\HestiaCP\Command\Lists\Users;
use neto737\HestiaCP\Command\ResponseCode;
use neto737\HestiaCP\InvalidResponseException;
use neto737\HestiaCP\ProcessException;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Responses mimic HestiaCP's web/api/index.php: the access key endpoint sets the
 * Hestia-Exit-Code header and an HTTP error status, the legacy endpoint only echoes the code.
 */
class clientSendTest extends \PHPUnit\Framework\TestCase {

	/** @var array */
	private $history = [];

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
			'hash' => 'ACCESS:SECRET',
			'arg1' => 'user',
			'arg2' => 'pass',
			'arg3' => 'mail@domain.com',
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

		$client->send(new LetsEncryptDomain('user', 'domain.com'));
		$this->assertEquals(90, $this->history[1]['options']['timeout']);

		$client->send(new \neto737\HestiaCP\Command\Backup\User('user'));
		$this->assertEquals(0, $this->history[2]['options']['timeout']);
	}

	public function testCommandTimeoutNeverShortensClientTimeout(): void {
		$client = $this->createClient([self::codeResponse(0)], ['timeout' => 120]);
		$client->send(new LetsEncryptDomain('user', 'domain.com'));
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
		$client->send(new Users());
	}

	public function testListErrorKeepsHestiaMessage(): void {
		$client = $this->createClient([
			new Response(400, ['Hestia-Exit-Code' => '3'], "Error: user someone doesn't exist")
		]);

		$this->expectException(ProcessException::class);
		$this->expectExceptionMessage("Object doesn't exist (Error: user someone doesn't exist)");
		$client->send(new User('someone'));
	}

	public function testUnexpectedHttpStatus(): void {
		$client = $this->createClient([new Response(502, [], '<html>Bad Gateway</html>')]);

		$this->expectException(InvalidResponseException::class);
		$client->send(new Users());
	}

	public function testNotHestiaResponse(): void {
		$client = $this->createClient([new Response(200, [], '<html>Hello</html>')]);

		$this->expectException(InvalidResponseException::class);
		$client->send(new AddUser('user', 'pass', 'mail@domain.com'));
	}

	public function testConnectionError(): void {
		$client = $this->createClient([new ConnectException('Connection refused', new Request('POST', 'test'))]);

		try {
			$client->send(new Users());
			$this->fail('ClientException expected');
		} catch (ClientException $e) {
			$this->assertInstanceOf(ConnectException::class, $e->getPrevious());
		}
	}

	public function testList(): void {
		$client = $this->createClient([
			new Response(200, ['Hestia-Exit-Code' => '0'], '{"admin": {"NAME": "System Administrator"}, "user": {"NAME": "User"}}')
		]);

		$users = $client->send(new Users());
		$this->assertSame(['admin', 'user'], array_keys($users));
		$this->assertInstanceOf(ArrayHash::class, $users['admin']);
		$this->assertSame('System Administrator', $users['admin']->NAME);
	}

	public function testListDetail(): void {
		$client = $this->createClient([new Response(200, [], '{"admin": {"NAME": "System Administrator"}}')]);

		$user = $client->send(new User('admin'));
		$this->assertInstanceOf(ArrayHash::class, $user);
		$this->assertSame('System Administrator', $user->NAME);
	}

	public function testListEmpty(): void {
		$client = $this->createClient([new Response(204, ['Hestia-Exit-Code' => '0'], '')]);
		$this->assertSame([], $client->send(new Users()));
	}

	public function testListInvalidJson(): void {
		$client = $this->createClient([new Response(200, [], 'not json')]);

		$this->expectException(ProcessException::class);
		$this->expectExceptionMessage('not json');
		$client->send(new Users());
	}

	public function testListDatabasesReturnsAll(): void {
		$client = $this->createClient([
			new Response(200, [], '{"admin_one": {"DBUSER": "admin_one"}, "admin_two": {"DBUSER": "admin_two"}}')
		]);

		$databases = $client->getModuleDatabase('admin')->listDatabases();
		$this->assertSame(['admin_one', 'admin_two'], array_keys($databases));
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
			self::codeResponse(ResponseCode::E_FORBIDEN, 401)
		]);

		$this->assertFalse($client->testAuthorization());
		$this->assertFalse($client->testAuthorization());
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
