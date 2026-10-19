<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use GuzzleHttp\Psr7\Response;
use neto737\HestiaCP\Command\Web\AddLetsEncryptDomain;
use neto737\HestiaCP\Command\Web\AddWebDomain;
use neto737\HestiaCP\Command\Web\AddWebDomainAlias;
use neto737\HestiaCP\Command\Web\AddWebDomainFtp;
use neto737\HestiaCP\Command\Web\AddWebDomainProxy;
use neto737\HestiaCP\Command\Web\AddWebDomainSsl;
use neto737\HestiaCP\Command\Web\AddWebDomainSslForce;
use neto737\HestiaCP\Command\Web\AddWebDomainSslHsts;
use neto737\HestiaCP\Command\Web\ChangeWebDomainBackendTpl;
use neto737\HestiaCP\Command\Web\ChangeWebDomainFtpPassword;
use neto737\HestiaCP\Command\Web\ChangeWebDomainFtpPath;
use neto737\HestiaCP\Command\Web\ChangeWebDomainProxyTpl;
use neto737\HestiaCP\Command\Web\ChangeWebDomainSslCert;
use neto737\HestiaCP\Command\Web\ChangeWebDomainSslHome;
use neto737\HestiaCP\Command\Web\ChangeWebDomainTpl;
use neto737\HestiaCP\Command\Web\DeleteLetsEncryptDomain;
use neto737\HestiaCP\Command\Web\DeleteWebDomain;
use neto737\HestiaCP\Command\Web\DeleteWebDomainAlias;
use neto737\HestiaCP\Command\Web\DeleteWebDomainFtp;
use neto737\HestiaCP\Command\Web\DeleteWebDomainProxy;
use neto737\HestiaCP\Command\Web\DeleteWebDomainSsl;
use neto737\HestiaCP\Command\Web\DeleteWebDomainSslForce;
use neto737\HestiaCP\Command\Web\DeleteWebDomainSslHsts;
use neto737\HestiaCP\Command\Web\ListWebDomain;
use neto737\HestiaCP\Command\Web\ListWebDomains;
use neto737\HestiaCP\Command\Web\ListWebDomainSsl;
use neto737\HestiaCP\Command\Web\ListWebTemplates;
use neto737\HestiaCP\Command\Web\ListWebTemplatesBackend;
use neto737\HestiaCP\Command\Web\ListWebTemplatesProxy;
use neto737\HestiaCP\Command\Web\SuspendWebDomain;
use neto737\HestiaCP\Command\Web\UnsuspendWebDomain;
use neto737\HestiaCP\Exceptions\InvalidArgumentException;

class WebTest extends \PHPUnit\Framework\TestCase {

	public function testListWebDomains(): void {
		$command = new ListWebDomains('admin');
		$this->assertSame('v-list-web-domains', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertSame([], $command->process(new Response()));
	}

	public function testListWebDomain(): void {
		$command = new ListWebDomain('admin', 'example.com');
		$this->assertSame('v-list-web-domain', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'json'], $command->getRequestParams());

		$this->assertFalse($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testListWebDomainSsl(): void {
		$command = new ListWebDomainSsl('admin', 'example.com');
		$this->assertSame('v-list-web-domain-ssl', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'json'], $command->getRequestParams());

		$this->assertFalse($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testListWebTemplates(): void {
		foreach ([
			'v-list-web-templates' => new ListWebTemplates(),
			'v-list-web-templates-backend' => new ListWebTemplatesBackend(),
			'v-list-web-templates-proxy' => new ListWebTemplatesProxy()
		] as $name => $command) {
			$this->assertSame($name, $command->getName());
			$this->assertSame(['arg1' => 'json'], $command->getRequestParams());

			$this->assertFalse($command->needReturnCode());
			$this->assertSame([], $command->process(new Response()));
		}
	}

	public function testAddWebDomain(): void {
		$command = new AddWebDomain('admin', 'domain.com');
		$this->assertSame('v-add-web-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => '',
			'arg4' => 'no',
			'arg5' => 'none'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testAddWebDomainWithIpAndProxyExtensions(): void {
		$command = new AddWebDomain('admin', 'domain.com', null, null, 'jpg,png');
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => '',
			'arg4' => 'no',
			'arg5' => 'none',
			'arg6' => 'jpg,png'
		], $command->getRequestParams());

		$command = new AddWebDomain('admin', 'domain.com', '127.0.0.1', 'www.domain.com', null, true);
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => '127.0.0.1',
			'arg4' => 'yes',
			'arg5' => 'www.domain.com'
		], $command->getRequestParams());
	}

	public function testAddWebDomainFtp(): void {
		$command = new AddWebDomainFtp('admin', 'domain.com', 'account', 'pass');
		$this->assertSame('v-add-web-domain-ftp', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account',
			'arg4' => 'pass'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testAddLetsEncryptDomain(): void {
		$command = new AddLetsEncryptDomain('admin', 'domain.com');
		$this->assertSame('v-add-letsencrypt-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => '',
			'arg4' => 'no'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testAddLetsEncryptDomainMailAndKeyAlgo(): void {
		$command = new AddLetsEncryptDomain('admin', 'domain.com', 'www.domain.com', true, AddLetsEncryptDomain::KEY_ALGO_RSA);
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'www.domain.com',
			'arg4' => 'yes',
			'arg5' => 'rsa'
		], $command->getRequestParams());
	}

	public function testAddLetsEncryptDomainInvalidKeyAlgo(): void {
		$this->expectException(InvalidArgumentException::class);
		new AddLetsEncryptDomain('admin', 'domain.com', null, false, 'dsa');
	}

	public function testChangeWebDomainFtpPassword(): void {
		$command = new ChangeWebDomainFtpPassword('admin', 'domain', 'account', 'pass');
		$this->assertSame('v-change-web-domain-ftp-password', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain',
			'arg3' => 'account',
			'arg4' => 'pass'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testChangeWebDomainFtpPath(): void {
		$command = new ChangeWebDomainFtpPath('admin', 'domain', 'account', 'path');
		$this->assertSame('v-change-web-domain-ftp-path', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain',
			'arg3' => 'account',
			'arg4' => 'path'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testDeleteWebDomain(): void {
		$command = new DeleteWebDomain('admin', 'domain.com');
		$this->assertSame('v-delete-web-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testDeleteWebDomainFtp(): void {
		$command = new DeleteWebDomainFtp('admin', 'domain.com', 'account');
		$this->assertSame('v-delete-web-domain-ftp', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'account'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testDeleteLetsEncryptDomain(): void {
		$command = new DeleteLetsEncryptDomain('admin', 'domain.com');
		$this->assertSame('v-delete-letsencrypt-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'no'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testSuspendWebDomain(): void {
		$command = new SuspendWebDomain('admin', 'domain.com');
		$this->assertSame('v-suspend-web-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'no'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testUnsuspendWebDomain(): void {
		$command = new UnsuspendWebDomain('admin', 'domain.com');
		$this->assertSame('v-unsuspend-web-domain', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'domain.com',
			'arg3' => 'no'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testAddWebDomainAlias(): void {
		$command = new AddWebDomainAlias('admin', 'example.com', 'www.example.com,shop.example.com');
		$this->assertSame('v-add-web-domain-alias', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'www.example.com,shop.example.com'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testDeleteWebDomainAlias(): void {
		$command = new DeleteWebDomainAlias('admin', 'example.com', 'www.example.com');
		$this->assertSame('v-delete-web-domain-alias', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'www.example.com'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testAddWebDomainSsl(): void {
		$command = new AddWebDomainSsl('admin', 'example.com', '/tmp/ssl');
		$this->assertSame('v-add-web-domain-ssl', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => '/tmp/ssl'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testAddWebDomainSslRestartWithoutSslHome(): void {
		// HestiaCP would store an empty SSL home, so its default is sent instead
		$command = new AddWebDomainSsl('admin', 'example.com', '/tmp/ssl', null, false);
		$this->assertSame(['arg4' => 'same', 'arg5' => 'no'], array_slice($command->getRequestParams(), 3));
	}

	public function testAddWebDomainSslInvalidSslHome(): void {
		$this->expectException(InvalidArgumentException::class);
		new AddWebDomainSsl('admin', 'example.com', '/tmp/ssl', 'public_shtml');
	}

	public function testChangeWebDomainSslCert(): void {
		$command = new ChangeWebDomainSslCert('admin', 'example.com', '/tmp/ssl');
		$this->assertSame('v-change-web-domain-sslcert', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => '/tmp/ssl'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testChangeWebDomainSslHome(): void {
		$command = new ChangeWebDomainSslHome('admin', 'example.com', AddWebDomainSsl::SSL_HOME_SINGLE);
		$this->assertSame('v-change-web-domain-sslhome', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'single'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testChangeWebDomainSslHomeInvalid(): void {
		$this->expectException(InvalidArgumentException::class);
		new ChangeWebDomainSslHome('admin', 'example.com', 'other');
	}

	public function testDomainOnlyCommands(): void {
		foreach ([
			'v-delete-web-domain-ssl' => new DeleteWebDomainSsl('admin', 'example.com'),
			'v-add-web-domain-ssl-force' => new AddWebDomainSslForce('admin', 'example.com'),
			'v-delete-web-domain-ssl-force' => new DeleteWebDomainSslForce('admin', 'example.com'),
			'v-add-web-domain-ssl-hsts' => new AddWebDomainSslHsts('admin', 'example.com'),
			'v-delete-web-domain-ssl-hsts' => new DeleteWebDomainSslHsts('admin', 'example.com'),
			'v-delete-web-domain-proxy' => new DeleteWebDomainProxy('admin', 'example.com')
		] as $name => $command) {
			$this->assertSame($name, $command->getName());
			$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com'], $command->getRequestParams());
			$this->assertTrue($command->needReturnCode());
		}
	}

	public function testChangeWebDomainTpl(): void {
		$command = new ChangeWebDomainTpl('admin', 'example.com', 'hosting');
		$this->assertSame('v-change-web-domain-tpl', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'hosting'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testChangeWebDomainBackendTpl(): void {
		$command = new ChangeWebDomainBackendTpl('admin', 'example.com', 'PHP-8_3');
		$this->assertSame('v-change-web-domain-backend-tpl', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'PHP-8_3'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testChangeWebDomainProxyTpl(): void {
		$command = new ChangeWebDomainProxyTpl('admin', 'example.com', 'caching');
		$this->assertSame('v-change-web-domain-proxy-tpl', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'caching'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testAddWebDomainProxy(): void {
		$command = new AddWebDomainProxy('admin', 'example.com');
		$this->assertSame('v-add-web-domain-proxy', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testAddWebDomainProxyExtensionsWithoutTemplate(): void {
		// an empty template makes HestiaCP use the user's package default
		$command = new AddWebDomainProxy('admin', 'example.com', null, 'jpg,png');
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => '', 'arg4' => 'jpg,png'], $command->getRequestParams());
	}

	public function testProxyRestartWithEmptyExtensions(): void {
		// no extensions is still allowed, it just has to be asked for
		$command = new AddWebDomainProxy('admin', 'example.com', null, '', true);
		$this->assertSame(['arg3' => '', 'arg4' => '', 'arg5' => 'yes'], array_slice($command->getRequestParams(), 2));
	}

	public function testAddWebDomainProxyRestartNeedsExtensions(): void {
		// HestiaCP reads an empty extensions argument as no extensions, not as its default list
		$this->expectException(InvalidArgumentException::class);
		new AddWebDomainProxy('admin', 'example.com', 'caching', null, false);
	}

	public function testChangeWebDomainProxyTplRestartNeedsExtensions(): void {
		$this->expectException(InvalidArgumentException::class);
		new ChangeWebDomainProxyTpl('admin', 'example.com', 'caching', null, true);
	}

	public function testRestartIsSentWhenSet(): void {
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'no'], (new DeleteWebDomainSsl('admin', 'example.com', false))->getRequestParams());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'yes'], (new AddWebDomainSslForce('admin', 'example.com', true))->getRequestParams());
		$this->assertSame(
			['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'www.example.com', 'arg4' => 'no'],
			(new DeleteWebDomainAlias('admin', 'example.com', 'www.example.com', false))->getRequestParams()
		);
	}
}
