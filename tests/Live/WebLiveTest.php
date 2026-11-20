<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Live;

use neto737\HestiaCP\Command\ResponseCode;
use neto737\HestiaCP\Command\Web\AddWebDomainSsl;
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

	public function testTemplate(): void {
		$web = self::web();
		$domain = self::domain('tpl');

		$this->assertTrue($web->addDomain($domain, self::ip()));
		$current = $web->listDomain($domain)['TPL'];
		$other = array_values(array_diff($web->listTemplates(), [$current]));
		if ($other === []) {
			$this->markTestSkipped('The server has only one web template');
		}

		$this->assertTrue($web->changeDomainTemplate($domain, $other[0]));
		$this->assertSame($other[0], $web->listDomain($domain)['TPL']);
		$this->assertTrue($web->changeDomainTemplate($domain, $current));
		$this->assertTrue($web->deleteDomain($domain));
	}

	public function testSsl(): void {
		if (!extension_loaded('openssl')) {
			$this->markTestSkipped('The openssl extension is needed to create a certificate');
		}

		$web = self::web();
		$domain = self::domain('ssl');
		$this->assertTrue($web->addDomain($domain, self::ip()));

		try {
			// HestiaCP's own web UI also writes the files to /tmp before running the script
			$first = self::uploadCertificate($domain);
			$this->assertTrue($web->addDomainSsl($domain, '/tmp', AddWebDomainSsl::SSL_HOME_SINGLE));
			$detail = $web->listDomain($domain);
			$this->assertSame(['yes', 'single'], [$detail['SSL'], $detail['SSL_HOME']]);
			$this->assertSame(openssl_x509_fingerprint($first, 'sha256'), openssl_x509_fingerprint($web->listDomainSsl($domain)['CRT'], 'sha256'));

			$this->assertTrue($web->changeDomainSslHome($domain, AddWebDomainSsl::SSL_HOME_SAME));
			$this->assertTrue($web->addDomainSslForce($domain));
			$this->assertTrue($web->addDomainSslHsts($domain));
			$detail = $web->listDomain($domain);
			$this->assertSame(['same', 'yes', 'yes'], [$detail['SSL_HOME'], $detail['SSL_FORCE'], $detail['SSL_HSTS']]);
			$this->assertTrue($web->deleteDomainSslHsts($domain));
			$this->assertTrue($web->deleteDomainSslForce($domain));
			$detail = $web->listDomain($domain);
			$this->assertSame(['no', 'no'], [$detail['SSL_FORCE'], $detail['SSL_HSTS']]);

			$second = self::uploadCertificate($domain);
			$this->assertTrue($web->changeDomainSslCert($domain, '/tmp'));
			$this->assertSame(openssl_x509_fingerprint($second, 'sha256'), openssl_x509_fingerprint($web->listDomainSsl($domain)['CRT'], 'sha256'));
		} finally {
			self::clearCertificate($domain);
		}

		$this->assertTrue($web->deleteDomainSsl($domain));
		$this->assertSame('no', $web->listDomain($domain)['SSL']);
		$this->assertTrue($web->deleteDomain($domain));
	}

	public function testDomainSettings(): void {
		$web = self::web();
		$domain = self::domain('webset');
		$this->assertTrue($web->addDomain($domain, self::ip()));

		$this->assertTrue($web->changeDomainIp($domain, self::ip()));
		$this->assertSame(self::ip(), $web->listDomain($domain)['IP']);

		$this->assertTrue($web->deleteDomain($domain));
	}

	public function testMissingDomain(): void {
		$this->expectException(ProcessException::class);
		$this->expectExceptionCode(ResponseCode::E_NOTEXIST);
		self::web()->listDomain(self::domain('missing'));
	}
}
