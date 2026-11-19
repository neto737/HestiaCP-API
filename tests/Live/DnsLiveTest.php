<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Live;

use neto737\HestiaCP\Command\ResponseCode;
use neto737\HestiaCP\Exceptions\ProcessException;

class DnsLiveTest extends LiveTestCase {

	public function testDomainAndRecords(): void {
		$dns = static::$client->getModuleDns(self::user());
		$domain = self::domain('dns');

		$this->assertTrue($dns->addDomain($domain, self::ip()));
		$this->assertSame(self::ip(), $dns->listDomain($domain)['IP']);

		$this->assertTrue($dns->addRecord($domain, 'mx2', 'MX', 'mx2.' . $domain . '.', 20, 90, null, 600));
		$record = $dns->listRecords($domain)['90'];
		$this->assertSame(['MX', '20', '600'], [$record['TYPE'], $record['PRIORITY'], $record['TTL']]);

		$this->assertTrue($dns->changeRecord($domain, 90, 'mx3', 'MX', 'mx3.' . $domain . '.', 30));
		$this->assertSame('mx3', $dns->listRecords($domain)['90']['RECORD']);
		$this->assertTrue($dns->changeRecordId($domain, 90, 91));
		$this->assertArrayHasKey('91', $dns->listRecords($domain));
		$this->assertTrue($dns->suspendRecord($domain, 91));
		$this->assertTrue($dns->unsuspendRecord($domain, 91));
		$this->assertTrue($dns->deleteRecord($domain, 91));
		$this->assertArrayNotHasKey('91', $dns->listRecords($domain));

		$this->assertTrue($dns->changeDomainTtl($domain, 7200));
		$this->assertTrue($dns->changeDomainExpiration($domain, '2030-01-31'));
		$this->assertTrue($dns->changeDomainSoa($domain, 'ns9.' . $domain));
		$detail = $dns->listDomain($domain);
		$this->assertSame(['7200', '2030-01-31', 'ns9.' . $domain], [$detail['TTL'], $detail['EXP'], $detail['SOA']]);

		$this->assertContains('default', $dns->listTemplates());
		$this->assertNotSame([], $dns->listTemplate('default'));

		$this->assertTrue($dns->suspendDomain($domain));
		$this->assertTrue($dns->unsuspendDomain($domain));
		$this->assertTrue($dns->deleteDomain($domain));

		$this->expectException(ProcessException::class);
		$this->expectExceptionCode(ResponseCode::E_NOTEXIST);
		$dns->listDomain($domain);
	}
}
