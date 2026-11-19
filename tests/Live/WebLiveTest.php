<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Live;

use neto737\HestiaCP\Command\ResponseCode;
use neto737\HestiaCP\Exceptions\ProcessException;
use neto737\HestiaCP\Module\Webs;

class WebLiveTest extends LiveTestCase {

	private static function web(): Webs {
		return static::$client->getModuleWeb(self::user());
	}

	public function testDomainLifecycle(): void {
		$web = self::web();
		$domain = self::domain('web');

		$this->assertSame([], $web->listDomains());
		$this->assertTrue($web->addDomain($domain, self::ip(), 'www.' . $domain, 'css,js'));
		$this->assertArrayHasKey($domain, $web->listDomains());

		$detail = $web->listDomain($domain);
		$this->assertSame('www.' . $domain, $detail['ALIAS']);
		$this->assertSame('css,js', $detail['PROXY_EXT']);

		try {
			$web->addDomain($domain);
			$this->fail('ProcessException expected');
		} catch (ProcessException $e) {
			$this->assertSame(ResponseCode::E_EXISTS, $e->getCode());
		}

		$this->assertTrue($web->addDomainAlias($domain, 'alias.' . $domain));
		$this->assertStringContainsString('alias.' . $domain, $web->listDomain($domain)['ALIAS']);
		$this->assertTrue($web->deleteDomainAlias($domain, 'alias.' . $domain));

		$this->assertContains('default', $web->listTemplates());
		$this->assertContains('default', $web->listBackendTemplates());
		$this->assertContains('caching', $web->listProxyTemplates());

		$this->assertTrue($web->changeDomainBackendTemplate($domain, 'no-php'));
		$this->assertSame('no-php', $web->listDomain($domain)['BACKEND']);
		$this->assertTrue($web->changeDomainProxyTemplate($domain, 'caching', 'jpg'));
		$this->assertSame('caching', $web->listDomain($domain)['PROXY']);
		$this->assertTrue($web->deleteDomainProxy($domain));
		$this->assertTrue($web->addDomainProxy($domain));

		$this->assertTrue($web->addDomainFtp($domain, 'ftp', self::password()));
		$ftpUser = self::user() . '_ftp';
		$this->assertStringContainsString($ftpUser, $web->listDomain($domain)['FTP_USER']);
		$this->assertTrue($web->changeDomainFtpPassword($domain, $ftpUser, self::password()));
		$this->assertTrue($web->changeDomainFtpPath($domain, $ftpUser, 'public_html/sub'));
		$this->assertTrue($web->deleteDomainFtp($domain, $ftpUser));

		$this->assertTrue($web->suspendDomain($domain));
		$this->assertSame('yes', $web->listDomain($domain)['SUSPENDED']);
		$this->assertTrue($web->unsuspendDomain($domain));

		$this->assertTrue($web->deleteDomain($domain));
		$this->assertSame([], $web->listDomains());
	}

	public function testMissingDomain(): void {
		$this->expectException(ProcessException::class);
		$this->expectExceptionCode(ResponseCode::E_NOTEXIST);
		self::web()->listDomain(self::domain('missing'));
	}
}
