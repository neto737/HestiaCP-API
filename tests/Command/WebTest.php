<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use neto737\HestiaCP\Command\Web\AddWebDomainAlias;
use neto737\HestiaCP\Command\Web\AddWebDomainProxy;
use neto737\HestiaCP\Command\Web\AddWebDomainSsl;
use neto737\HestiaCP\Command\Web\AddWebDomainSslForce;
use neto737\HestiaCP\Command\Web\AddWebDomainSslHsts;
use neto737\HestiaCP\Command\Web\ChangeWebDomainBackendTpl;
use neto737\HestiaCP\Command\Web\ChangeWebDomainProxyTpl;
use neto737\HestiaCP\Command\Web\ChangeWebDomainSslCert;
use neto737\HestiaCP\Command\Web\ChangeWebDomainSslHome;
use neto737\HestiaCP\Command\Web\ChangeWebDomainTpl;
use neto737\HestiaCP\Command\Web\DeleteWebDomainAlias;
use neto737\HestiaCP\Command\Web\DeleteWebDomainProxy;
use neto737\HestiaCP\Command\Web\DeleteWebDomainSsl;
use neto737\HestiaCP\Command\Web\DeleteWebDomainSslForce;
use neto737\HestiaCP\Command\Web\DeleteWebDomainSslHsts;
use neto737\HestiaCP\Command\Web\ListWebDomain;
use neto737\HestiaCP\Command\Web\ListWebDomainSsl;
use neto737\HestiaCP\Command\Web\ListWebTemplates;
use neto737\HestiaCP\Command\Web\ListWebTemplatesBackend;
use neto737\HestiaCP\Command\Web\ListWebTemplatesProxy;
use neto737\HestiaCP\Exceptions\InvalidArgumentException;

class WebTest extends \PHPUnit\Framework\TestCase {

	public function testListWebDomain(): void {
		$command = new ListWebDomain('admin', 'example.com');
		$this->assertSame('v-list-web-domain', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'json'], $command->getRequestParams());

		$this->assertFalse($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process();
	}

	public function testListWebDomainSsl(): void {
		$command = new ListWebDomainSsl('admin', 'example.com');
		$this->assertSame('v-list-web-domain-ssl', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'json'], $command->getRequestParams());

		$this->assertFalse($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process();
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
			$this->assertSame([], $command->process());
		}
	}

	public function testAddWebDomainAlias(): void {
		$command = new AddWebDomainAlias('admin', 'example.com', 'www.example.com,shop.example.com');
		$this->assertSame('v-add-web-domain-alias', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'www.example.com,shop.example.com'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process();
	}

	public function testDeleteWebDomainAlias(): void {
		$command = new DeleteWebDomainAlias('admin', 'example.com', 'www.example.com');
		$this->assertSame('v-delete-web-domain-alias', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'www.example.com'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process();
	}

	public function testAddWebDomainSsl(): void {
		$command = new AddWebDomainSsl('admin', 'example.com', '/tmp/ssl');
		$this->assertSame('v-add-web-domain-ssl', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => '/tmp/ssl'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process();
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
		$command->process();
	}

	public function testChangeWebDomainSslHome(): void {
		$command = new ChangeWebDomainSslHome('admin', 'example.com', AddWebDomainSsl::SSL_HOME_SINGLE);
		$this->assertSame('v-change-web-domain-sslhome', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'single'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process();
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
		$command->process();
	}

	public function testChangeWebDomainBackendTpl(): void {
		$command = new ChangeWebDomainBackendTpl('admin', 'example.com', 'PHP-8_3');
		$this->assertSame('v-change-web-domain-backend-tpl', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'PHP-8_3'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process();
	}

	public function testChangeWebDomainProxyTpl(): void {
		$command = new ChangeWebDomainProxyTpl('admin', 'example.com', 'caching');
		$this->assertSame('v-change-web-domain-proxy-tpl', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'caching'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process();
	}

	public function testAddWebDomainProxy(): void {
		$command = new AddWebDomainProxy('admin', 'example.com');
		$this->assertSame('v-add-web-domain-proxy', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process();
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
