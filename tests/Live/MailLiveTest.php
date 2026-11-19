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
}
