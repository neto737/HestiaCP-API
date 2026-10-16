<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Module;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use neto737\HestiaCP\Client;
use neto737\HestiaCP\Module\Backups;
use neto737\HestiaCP\Module\Crons;
use neto737\HestiaCP\Module\Databases;
use neto737\HestiaCP\Module\Mails;
use neto737\HestiaCP\Module\Packages;
use neto737\HestiaCP\Module\Users;
use neto737\HestiaCP\Module\Webs;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Calls every module method through a mocked client and checks the request it sends and the value it returns.
 * Arguments use distinct values (and non-default booleans) so a swapped or dropped argument fails here.
 *
 * @phpstan-type HistoryEntry array{request: RequestInterface, response: ResponseInterface|null, error: mixed, options: array<array-key, mixed>}
 * @phpstan-type ModuleCall \Closure(Client): mixed
 */
class ModuleSendTest extends \PHPUnit\Framework\TestCase {

	private const LIST_BODY = '{"first": {"NAME": "First"}, "second": {"NAME": "Second"}}';

	/** @var array<array-key, HistoryEntry> */
	private array $history = [];

	private function createClient(Response $response): Client {
		$this->history = [];
		$stack = HandlerStack::create(new MockHandler([$response]));
		$stack->push(Middleware::history($this->history));

		return Client::simpleFactory('https://someHost', 'ACCESS:SECRET', null, ['handler' => $stack]);
	}

	/**
	 * @param array<string, string> $args
	 * @param 'yes'|'no' $returnCode
	 */
	private function assertRequest(string $cmd, array $args, string $returnCode): void {
		$this->assertCount(1, $this->history);

		parse_str((string) $this->history[0]['request']->getBody(), $params);
		$this->assertSame(['access_key' => 'ACCESS', 'secret_key' => 'SECRET'] + $args + ['returncode' => $returnCode, 'cmd' => $cmd], $params);
	}

	/**
	 * Methods that run a command and return true on success.
	 *
	 * @return iterable<string, array{ModuleCall, string, array<string, string>}>
	 */
	public static function processProvider(): iterable {
		yield 'Users::add' => [
			fn (Client $c) => $c->getModuleUser()->add('bob', 'secret', 'bob@example.com', 'gold', 'Bob', 'Smith'),
			'v-add-user',
			['arg1' => 'bob', 'arg2' => 'secret', 'arg3' => 'bob@example.com', 'arg4' => 'gold', 'arg5' => 'Bob', 'arg6' => 'Smith']
		];
		yield 'Users::changePassword' => [
			fn (Client $c) => $c->getModuleUser()->changePassword('bob', 'secret'),
			'v-change-user-password',
			['arg1' => 'bob', 'arg2' => 'secret']
		];
		yield 'Users::changeContact' => [
			fn (Client $c) => $c->getModuleUser()->changeContact('bob', 'bob@example.com'),
			'v-change-user-contact',
			['arg1' => 'bob', 'arg2' => 'bob@example.com']
		];
		yield 'Users::changePackage' => [
			fn (Client $c) => $c->getModuleUser()->changePackage('bob', 'gold', true),
			'v-change-user-package',
			['arg1' => 'bob', 'arg2' => 'gold', 'arg3' => 'yes']
		];
		yield 'Users::suspend' => [
			fn (Client $c) => $c->getModuleUser()->suspend('bob', true),
			'v-suspend-user',
			['arg1' => 'bob', 'arg2' => 'yes']
		];
		yield 'Users::unsuspend' => [
			fn (Client $c) => $c->getModuleUser()->unsuspend('bob', true),
			'v-unsuspend-user',
			['arg1' => 'bob', 'arg2' => 'yes']
		];
		yield 'Users::delete' => [
			fn (Client $c) => $c->getModuleUser()->delete('bob'),
			'v-delete-user',
			['arg1' => 'bob']
		];
		yield 'Users::backup' => [
			fn (Client $c) => $c->getModuleUser()->backup(),
			'v-backup-users',
			[]
		];

		yield 'Backups::backup' => [
			fn (Client $c) => $c->getModuleBackup('alice')->backup(false),
			'v-backup-user',
			['arg1' => 'alice', 'arg2' => 'no']
		];
		yield 'Backups::delete' => [
			fn (Client $c) => $c->getModuleBackup('alice')->delete('alice.2026-10-15.tar'),
			'v-delete-user-backup',
			['arg1' => 'alice', 'arg2' => 'alice.2026-10-15.tar']
		];
		yield 'Backups::deleteExclusions' => [
			fn (Client $c) => $c->getModuleBackup('alice')->deleteExclusions('web'),
			'v-delete-user-backup-exclusions',
			['arg1' => 'alice', 'arg2' => 'web']
		];

		yield 'Databases::add' => [
			fn (Client $c) => $c->getModuleDatabase('alice')->add('alice_db', 'alice_dbuser', 'secret', 'pgsql'),
			'v-add-database',
			['arg1' => 'alice', 'arg2' => 'alice_db', 'arg3' => 'alice_dbuser', 'arg4' => 'secret', 'arg5' => 'pgsql']
		];
		yield 'Databases::changePassword' => [
			fn (Client $c) => $c->getModuleDatabase('alice')->changePassword('alice_db', 'secret'),
			'v-change-database-password',
			['arg1' => 'alice', 'arg2' => 'alice_db', 'arg3' => 'secret']
		];
		yield 'Databases::suspend' => [
			fn (Client $c) => $c->getModuleDatabase('alice')->suspend('alice_db'),
			'v-suspend-database',
			['arg1' => 'alice', 'arg2' => 'alice_db']
		];
		yield 'Databases::unsuspend' => [
			fn (Client $c) => $c->getModuleDatabase('alice')->unsuspend('alice_db'),
			'v-unsuspend-database',
			['arg1' => 'alice', 'arg2' => 'alice_db']
		];
		yield 'Databases::delete' => [
			fn (Client $c) => $c->getModuleDatabase('alice')->delete('alice_db'),
			'v-delete-database',
			['arg1' => 'alice', 'arg2' => 'alice_db']
		];
		yield 'Databases::deleteDatabases' => [
			fn (Client $c) => $c->getModuleDatabase('alice')->deleteDatabases(),
			'v-delete-databases',
			['arg1' => 'alice']
		];

		yield 'Crons::addJob' => [
			fn (Client $c) => $c->getModuleCron('alice')->addJob('0', '3', '1', '2', '1-5', 'uptime', 12, true),
			'v-add-cron-job',
			['arg1' => 'alice', 'arg2' => '0', 'arg3' => '3', 'arg4' => '1', 'arg5' => '2', 'arg6' => '1-5', 'arg7' => 'uptime', 'arg8' => '12', 'arg9' => 'yes']
		];
		yield 'Crons::changeJob' => [
			fn (Client $c) => $c->getModuleCron('alice')->changeJob(12, '0', '3', '1', '2', '1-5', 'uptime'),
			'v-change-cron-job',
			['arg1' => 'alice', 'arg2' => '12', 'arg3' => '0', 'arg4' => '3', 'arg5' => '1', 'arg6' => '2', 'arg7' => '1-5', 'arg8' => 'uptime']
		];
		yield 'Crons::suspendJob' => [
			fn (Client $c) => $c->getModuleCron('alice')->suspendJob(12, true),
			'v-suspend-cron-job',
			['arg1' => 'alice', 'arg2' => '12', 'arg3' => 'yes']
		];
		yield 'Crons::unsuspendJob' => [
			fn (Client $c) => $c->getModuleCron('alice')->unsuspendJob(12, false),
			'v-unsuspend-cron-job',
			['arg1' => 'alice', 'arg2' => '12', 'arg3' => 'no']
		];
		yield 'Crons::deleteJob' => [
			fn (Client $c) => $c->getModuleCron('alice')->deleteJob(12, true),
			'v-delete-cron-job',
			['arg1' => 'alice', 'arg2' => '12', 'arg3' => 'yes']
		];

		yield 'Mails::addAccount' => [
			fn (Client $c) => $c->getModuleMail('alice')->addAccount('example.com', 'info', 'secret'),
			'v-add-mail-account',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'info', 'arg4' => 'secret']
		];
		yield 'Mails::changeAccountPassword' => [
			fn (Client $c) => $c->getModuleMail('alice')->changeAccountPassword('example.com', 'info', 'secret'),
			'v-change-mail-account-password',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'info', 'arg4' => 'secret']
		];
		yield 'Mails::suspendAccount' => [
			fn (Client $c) => $c->getModuleMail('alice')->suspendAccount('example.com', 'info'),
			'v-suspend-mail-account',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'info']
		];
		yield 'Mails::unsuspendAccount' => [
			fn (Client $c) => $c->getModuleMail('alice')->unsuspendAccount('example.com', 'info'),
			'v-unsuspend-mail-account',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'info']
		];
		yield 'Mails::deleteAccount' => [
			fn (Client $c) => $c->getModuleMail('alice')->deleteAccount('example.com', 'info'),
			'v-delete-mail-account',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'info']
		];
		yield 'Mails::addDomain' => [
			fn (Client $c) => $c->getModuleMail('alice')->addDomain('example.com', false, true, false),
			'v-add-mail-domain',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'no', 'arg4' => 'yes', 'arg5' => 'no']
		];
		yield 'Mails::suspendDomain' => [
			fn (Client $c) => $c->getModuleMail('alice')->suspendDomain('example.com'),
			'v-suspend-mail-domain',
			['arg1' => 'alice', 'arg2' => 'example.com']
		];
		yield 'Mails::unsuspendDomain' => [
			fn (Client $c) => $c->getModuleMail('alice')->unsuspendDomain('example.com'),
			'v-unsuspend-mail-domain',
			['arg1' => 'alice', 'arg2' => 'example.com']
		];
		yield 'Mails::deleteDomain' => [
			fn (Client $c) => $c->getModuleMail('alice')->deleteDomain('example.com'),
			'v-delete-mail-domain',
			['arg1' => 'alice', 'arg2' => 'example.com']
		];

		yield 'Webs::addDomain' => [
			fn (Client $c) => $c->getModuleWeb('alice')->addDomain('example.com', '10.0.0.1', 'www.example.com', 'jpg,png', true),
			'v-add-web-domain',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => '10.0.0.1', 'arg4' => 'yes', 'arg5' => 'www.example.com', 'arg6' => 'jpg,png']
		];
		yield 'Webs::suspendDomain' => [
			fn (Client $c) => $c->getModuleWeb('alice')->suspendDomain('example.com', true),
			'v-suspend-web-domain',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'yes']
		];
		yield 'Webs::unsuspendDomain' => [
			fn (Client $c) => $c->getModuleWeb('alice')->unsuspendDomain('example.com', true),
			'v-unsuspend-web-domain',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'yes']
		];
		yield 'Webs::deleteDomain' => [
			fn (Client $c) => $c->getModuleWeb('alice')->deleteDomain('example.com'),
			'v-delete-web-domain',
			['arg1' => 'alice', 'arg2' => 'example.com']
		];
		yield 'Webs::addDomainLetsEncrypt' => [
			fn (Client $c) => $c->getModuleWeb('alice')->addDomainLetsEncrypt('example.com', 'www.example.com', true, 'rsa'),
			'v-add-letsencrypt-domain',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'www.example.com', 'arg4' => 'yes', 'arg5' => 'rsa']
		];
		yield 'Webs::deleteDomainLetsEncrypt' => [
			fn (Client $c) => $c->getModuleWeb('alice')->deleteDomainLetsEncrypt('example.com', true),
			'v-delete-letsencrypt-domain',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'yes']
		];
		yield 'Webs::addDomainFtp' => [
			fn (Client $c) => $c->getModuleWeb('alice')->addDomainFtp('example.com', 'ftpuser', 'secret', '/public_html'),
			'v-add-web-domain-ftp',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'ftpuser', 'arg4' => 'secret', 'arg5' => '/public_html']
		];
		yield 'Webs::changeDomainFtpPassword' => [
			fn (Client $c) => $c->getModuleWeb('alice')->changeDomainFtpPassword('example.com', 'ftpuser', 'secret'),
			'v-change-web-domain-ftp-password',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'ftpuser', 'arg4' => 'secret']
		];
		yield 'Webs::changeDomainFtpPath' => [
			fn (Client $c) => $c->getModuleWeb('alice')->changeDomainFtpPath('example.com', 'ftpuser', '/public_html'),
			'v-change-web-domain-ftp-path',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'ftpuser', 'arg4' => '/public_html']
		];
		yield 'Webs::deleteDomainFtp' => [
			fn (Client $c) => $c->getModuleWeb('alice')->deleteDomainFtp('example.com', 'ftpuser'),
			'v-delete-web-domain-ftp',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'ftpuser']
		];
		yield 'Webs::addDomainAlias' => [
			fn (Client $c) => $c->getModuleWeb('alice')->addDomainAlias('example.com', 'www.example.com,shop.example.com', true),
			'v-add-web-domain-alias',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'www.example.com,shop.example.com', 'arg4' => 'yes']
		];
		yield 'Webs::deleteDomainAlias' => [
			fn (Client $c) => $c->getModuleWeb('alice')->deleteDomainAlias('example.com', 'www.example.com', false),
			'v-delete-web-domain-alias',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'www.example.com', 'arg4' => 'no']
		];
		yield 'Webs::addDomainSsl' => [
			fn (Client $c) => $c->getModuleWeb('alice')->addDomainSsl('example.com', '/tmp/ssl', 'single', true),
			'v-add-web-domain-ssl',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => '/tmp/ssl', 'arg4' => 'single', 'arg5' => 'yes']
		];
		yield 'Webs::changeDomainSslCert' => [
			fn (Client $c) => $c->getModuleWeb('alice')->changeDomainSslCert('example.com', '/tmp/ssl', true),
			'v-change-web-domain-sslcert',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => '/tmp/ssl', 'arg4' => 'yes']
		];
		yield 'Webs::changeDomainSslHome' => [
			fn (Client $c) => $c->getModuleWeb('alice')->changeDomainSslHome('example.com', 'single', true),
			'v-change-web-domain-sslhome',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'single', 'arg4' => 'yes']
		];
		yield 'Webs::deleteDomainSsl' => [
			fn (Client $c) => $c->getModuleWeb('alice')->deleteDomainSsl('example.com', true),
			'v-delete-web-domain-ssl',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'yes']
		];
		yield 'Webs::addDomainSslForce' => [
			fn (Client $c) => $c->getModuleWeb('alice')->addDomainSslForce('example.com', true),
			'v-add-web-domain-ssl-force',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'yes']
		];
		yield 'Webs::deleteDomainSslForce' => [
			fn (Client $c) => $c->getModuleWeb('alice')->deleteDomainSslForce('example.com', true),
			'v-delete-web-domain-ssl-force',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'yes']
		];
		yield 'Webs::addDomainSslHsts' => [
			fn (Client $c) => $c->getModuleWeb('alice')->addDomainSslHsts('example.com', true),
			'v-add-web-domain-ssl-hsts',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'yes']
		];
		yield 'Webs::deleteDomainSslHsts' => [
			fn (Client $c) => $c->getModuleWeb('alice')->deleteDomainSslHsts('example.com', true),
			'v-delete-web-domain-ssl-hsts',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'yes']
		];
		yield 'Webs::changeDomainTemplate' => [
			fn (Client $c) => $c->getModuleWeb('alice')->changeDomainTemplate('example.com', 'hosting', true),
			'v-change-web-domain-tpl',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'hosting', 'arg4' => 'yes']
		];
		yield 'Webs::changeDomainBackendTemplate' => [
			fn (Client $c) => $c->getModuleWeb('alice')->changeDomainBackendTemplate('example.com', 'PHP-8_3', true),
			'v-change-web-domain-backend-tpl',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'PHP-8_3', 'arg4' => 'yes']
		];
		yield 'Webs::changeDomainProxyTemplate' => [
			fn (Client $c) => $c->getModuleWeb('alice')->changeDomainProxyTemplate('example.com', 'caching', 'jpg,png', true),
			'v-change-web-domain-proxy-tpl',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'caching', 'arg4' => 'jpg,png', 'arg5' => 'yes']
		];
		yield 'Webs::addDomainProxy' => [
			fn (Client $c) => $c->getModuleWeb('alice')->addDomainProxy('example.com', 'caching', 'jpg,png', true),
			'v-add-web-domain-proxy',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'caching', 'arg4' => 'jpg,png', 'arg5' => 'yes']
		];
		yield 'Webs::deleteDomainProxy' => [
			fn (Client $c) => $c->getModuleWeb('alice')->deleteDomainProxy('example.com', true),
			'v-delete-web-domain-proxy',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'yes']
		];
	}

	/**
	 * Methods that return a list, or a single entry of it (detail).
	 *
	 * @return iterable<string, array{ModuleCall, string, array<string, string>, bool}>
	 */
	public static function listProvider(): iterable {
		yield 'Users::list' => [
			fn (Client $c) => $c->getModuleUser()->list(),
			'v-list-users',
			['arg1' => 'json'],
			false
		];
		yield 'Users::detail' => [
			fn (Client $c) => $c->getModuleUser()->detail('bob'),
			'v-list-user',
			['arg1' => 'bob', 'arg2' => 'json'],
			true
		];
		yield 'Users::stats' => [
			fn (Client $c) => $c->getModuleUser()->stats('bob'),
			'v-list-user-stats',
			['arg1' => 'bob', 'arg2' => 'json'],
			false
		];

		yield 'Packages::list' => [
			fn (Client $c) => $c->getModulePackage()->list(),
			'v-list-user-packages',
			['arg1' => 'json'],
			false
		];
		yield 'Packages::detail' => [
			fn (Client $c) => $c->getModulePackage()->detail('gold'),
			'v-list-user-package',
			['arg1' => 'gold', 'arg2' => 'json'],
			true
		];

		yield 'Backups::listBackups' => [
			fn (Client $c) => $c->getModuleBackup('alice')->listBackups(),
			'v-list-user-backups',
			['arg1' => 'alice', 'arg2' => 'json'],
			false
		];
		yield 'Backups::listBackup' => [
			fn (Client $c) => $c->getModuleBackup('alice')->listBackup('alice.2026-10-15.tar'),
			'v-list-user-backup',
			['arg1' => 'alice', 'arg2' => 'alice.2026-10-15.tar', 'arg3' => 'json'],
			true
		];
		yield 'Backups::listBackupExclusions' => [
			fn (Client $c) => $c->getModuleBackup('alice')->listBackupExclusions(),
			'v-list-user-backup-exclusions',
			['arg1' => 'alice', 'arg2' => 'json'],
			false
		];

		yield 'Databases::listDatabase' => [
			fn (Client $c) => $c->getModuleDatabase('alice')->listDatabase('alice_db'),
			'v-list-database',
			['arg1' => 'alice', 'arg2' => 'alice_db', 'arg3' => 'json'],
			true
		];
		yield 'Databases::listDatabases' => [
			fn (Client $c) => $c->getModuleDatabase('alice')->listDatabases(),
			'v-list-databases',
			['arg1' => 'alice', 'arg2' => 'json'],
			false
		];

		yield 'Crons::listJobs' => [
			fn (Client $c) => $c->getModuleCron('alice')->listJobs(),
			'v-list-cron-jobs',
			['arg1' => 'alice', 'arg2' => 'json'],
			false
		];
		yield 'Crons::listJob' => [
			fn (Client $c) => $c->getModuleCron('alice')->listJob(12),
			'v-list-cron-job',
			['arg1' => 'alice', 'arg2' => '12', 'arg3' => 'json'],
			true
		];

		yield 'Mails::listAccounts' => [
			fn (Client $c) => $c->getModuleMail('alice')->listAccounts('example.com'),
			'v-list-mail-accounts',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'json'],
			false
		];
		yield 'Mails::listDomainDkim' => [
			fn (Client $c) => $c->getModuleMail('alice')->listDomainDkim('example.com'),
			'v-list-mail-domain-dkim',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'json'],
			false
		];
		yield 'Mails::listDomainDkimDns' => [
			fn (Client $c) => $c->getModuleMail('alice')->listDomainDkimDns('example.com'),
			'v-list-mail-domain-dkim-dns',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'json'],
			false
		];
		yield 'Mails::listDomains' => [
			fn (Client $c) => $c->getModuleMail('alice')->listDomains(),
			'v-list-mail-domains',
			['arg1' => 'alice', 'arg2' => 'json'],
			false
		];

		yield 'Webs::listDomains' => [
			fn (Client $c) => $c->getModuleWeb('alice')->listDomains(),
			'v-list-web-domains',
			['arg1' => 'alice', 'arg2' => 'json'],
			false
		];
		yield 'Webs::listDomain' => [
			fn (Client $c) => $c->getModuleWeb('alice')->listDomain('example.com'),
			'v-list-web-domain',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'json'],
			true
		];
		yield 'Webs::listDomainSsl' => [
			fn (Client $c) => $c->getModuleWeb('alice')->listDomainSsl('example.com'),
			'v-list-web-domain-ssl',
			['arg1' => 'alice', 'arg2' => 'example.com', 'arg3' => 'json'],
			true
		];
		yield 'Webs::listTemplates' => [
			fn (Client $c) => $c->getModuleWeb('alice')->listTemplates(),
			'v-list-web-templates',
			['arg1' => 'json'],
			false
		];
		yield 'Webs::listBackendTemplates' => [
			fn (Client $c) => $c->getModuleWeb('alice')->listBackendTemplates(),
			'v-list-web-templates-backend',
			['arg1' => 'json'],
			false
		];
		yield 'Webs::listProxyTemplates' => [
			fn (Client $c) => $c->getModuleWeb('alice')->listProxyTemplates(),
			'v-list-web-templates-proxy',
			['arg1' => 'json'],
			false
		];
	}

	/**
	 * @param ModuleCall $call
	 * @param array<string, string> $args
	 */
	#[DataProvider('processProvider')]
	public function testProcessMethod(\Closure $call, string $cmd, array $args): void {
		$client = $this->createClient(new Response(200, ['Hestia-Exit-Code' => '0'], '0'));

		$this->assertTrue($call($client));
		$this->assertRequest($cmd, $args, 'yes');
	}

	/**
	 * @param ModuleCall $call
	 * @param array<string, string> $args
	 */
	#[DataProvider('listProvider')]
	public function testListMethod(\Closure $call, string $cmd, array $args, bool $detail): void {
		$client = $this->createClient(new Response(200, ['Hestia-Exit-Code' => '0'], self::LIST_BODY));

		$expected = ['first' => ['NAME' => 'First'], 'second' => ['NAME' => 'Second']];
		$this->assertSame($detail ? $expected['first'] : $expected, $call($client));
		$this->assertRequest($cmd, $args, 'no');
	}

	/**
	 * Keeps the providers in sync with the modules, so a new module method can't be added without a test.
	 */
	public function testEveryModuleMethodIsCovered(): void {
		$methods = [];
		foreach ([Users::class, Backups::class, Databases::class, Mails::class, Webs::class, Packages::class, Crons::class] as $module) {
			$reflection = new \ReflectionClass($module);
			foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
				if ($method->getDeclaringClass()->getName() === $module && !$method->isConstructor()) {
					$methods[] = $reflection->getShortName() . '::' . $method->getName();
				}
			}
		}

		$tested = [...array_keys(iterator_to_array(self::processProvider())), ...array_keys(iterator_to_array(self::listProvider()))];

		sort($methods);
		sort($tested);
		$this->assertSame($methods, $tested);
	}
}
