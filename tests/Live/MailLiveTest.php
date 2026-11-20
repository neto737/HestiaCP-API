<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Live;

use neto737\HestiaCP\Command\ResponseCode;
use neto737\HestiaCP\Exceptions\ProcessException;

class MailLiveTest extends LiveTestCase {

	public function testDomainAndAccounts(): void {
		$mail = static::$client->getModuleMail(self::user());
		$domain = self::domain('mail');

		$this->assertTrue($mail->addDomain($domain, dkimSize: 1024, dkimSelector: 'live'));
		$this->assertSame('live', $mail->listDomain($domain)['DKIM_SELECTOR']);
		$this->assertArrayHasKey('live._domainkey', $mail->listDomainDkimDns($domain));
		$this->assertNotSame([], $mail->listDomainDkim($domain));

		$this->assertTrue($mail->addAccount($domain, 'info', self::password(), 512));
		$this->assertSame('512', $mail->listAccount($domain, 'info')['QUOTA']);
		$this->assertArrayHasKey('info', $mail->listAccounts($domain));
		$this->assertTrue($mail->changeAccountPassword($domain, 'info', self::password()));

		$this->assertTrue($mail->addAccountAlias($domain, 'info', 'contact'));
		$this->assertSame('contact', $mail->listAccount($domain, 'info')['ALIAS']);
		$this->assertTrue($mail->deleteAccountAlias($domain, 'info', 'contact'));

		$this->assertTrue($mail->addAccountForward($domain, 'info', 'someone@example.net'));
		$this->assertTrue($mail->addAccountForwardOnly($domain, 'info'));
		$this->assertSame('yes', $mail->listAccount($domain, 'info')['FWD_ONLY']);
		$this->assertTrue($mail->deleteAccountForwardOnly($domain, 'info'));
		$this->assertTrue($mail->deleteAccountForward($domain, 'info', 'someone@example.net'));

		$this->assertTrue($mail->addAccountAutoreply($domain, 'info', 'Away until Monday.'));
		$this->assertStringContainsString('Away until Monday', (string) json_encode($mail->listAccountAutoreply($domain, 'info')));
		$this->assertTrue($mail->deleteAccountAutoreply($domain, 'info'));

		$this->assertTrue($mail->suspendAccount($domain, 'info'));
		$this->assertTrue($mail->unsuspendAccount($domain, 'info'));
		$this->assertTrue($mail->suspendDomain($domain));
		$this->assertTrue($mail->unsuspendDomain($domain));

		$this->assertTrue($mail->deleteAccount($domain, 'info'));
		try {
			$mail->listAccount($domain, 'info');
			$this->fail('ProcessException expected');
		} catch (ProcessException $e) {
			$this->assertSame(ResponseCode::E_NOTEXIST, $e->getCode());
		}

		$this->assertTrue($mail->deleteDomain($domain));
		$this->assertSame([], $mail->listDomains());
	}

	public function testDomainSettings(): void {
		$mail = static::$client->getModuleMail(self::user());
		$domain = self::domain('mailset');

		// without SpamAssassin or ClamAV, HestiaCP adds the domain with both off
		$this->assertTrue($mail->addDomain($domain, false, false, true));
		$this->assertTrue($mail->deleteDomainDkim($domain));
		$detail = $mail->listDomain($domain);
		$this->assertSame(['no', 'no', 'no'], [$detail['ANTISPAM'], $detail['ANTIVIRUS'], $detail['DKIM']]);

		$this->assertTrue($mail->addDomainAntispam($domain));
		$this->assertTrue($mail->addDomainAntivirus($domain));
		$this->assertTrue($mail->addDomainDkim($domain, 1024, 'sel2'));
		$this->assertTrue($mail->addDomainRejectSpam($domain));
		$detail = $mail->listDomain($domain);
		$this->assertSame(['yes', 'yes', 'yes', 'sel2', 'yes'], [$detail['ANTISPAM'], $detail['ANTIVIRUS'], $detail['DKIM'], $detail['DKIM_SELECTOR'], $detail['REJECT']]);
		$this->assertArrayHasKey('sel2._domainkey', $mail->listDomainDkimDns($domain));
		$this->assertTrue($mail->deleteDomainRejectSpam($domain));
		$this->assertTrue($mail->deleteDomainAntispam($domain));
		$this->assertTrue($mail->deleteDomainAntivirus($domain));
		$detail = $mail->listDomain($domain);
		$this->assertSame(['no', 'no', 'no'], [$detail['REJECT'], $detail['ANTISPAM'], $detail['ANTIVIRUS']]);

		$this->assertTrue($mail->addDomainCatchall($domain, 'all@example.net'));
		$this->assertSame('all@example.net', $mail->listDomain($domain)['CATCHALL']);
		$this->assertTrue($mail->changeDomainCatchall($domain, 'other@example.net'));
		$this->assertSame('other@example.net', $mail->listDomain($domain)['CATCHALL']);
		$this->assertTrue($mail->deleteDomainCatchall($domain));
		$this->assertSame('', $mail->listDomain($domain)['CATCHALL']);

		$this->assertTrue($mail->addAccount($domain, 'quota', self::password(), 100));
		$this->assertTrue($mail->changeAccountQuota($domain, 'quota', 250));
		$this->assertSame('250', $mail->listAccount($domain, 'quota')['QUOTA']);
		$this->assertTrue($mail->changeAccountQuota($domain, 'quota', 0));
		$this->assertSame('unlimited', $mail->listAccount($domain, 'quota')['QUOTA']);

		$this->assertTrue($mail->deleteDomain($domain));
	}
}
