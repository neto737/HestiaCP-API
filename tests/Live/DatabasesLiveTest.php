<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Live;

use neto737\HestiaCP\Command\ResponseCode;
use neto737\HestiaCP\Exceptions\ProcessException;

class DatabasesLiveTest extends LiveTestCase {

	public function testDatabaseLifecycle(): void {
		$db = static::$client->getModuleDatabase(self::user());
		$name = self::user() . '_main';

		$this->assertSame([], $db->listDatabases());
		$this->assertTrue($db->add('main', 'dbu', self::password(), charset: 'LATIN1'));

		$detail = $db->listDatabase($name);
		$this->assertSame(self::user() . '_dbu', $detail['DBUSER']);
		$this->assertSame('LATIN1', $detail['CHARSET']);

		$this->assertTrue($db->changePassword($name, self::password()));
		$this->assertTrue($db->suspend($name));
		$this->assertSame('yes', $db->listDatabase($name)['SUSPENDED']);
		$this->assertTrue($db->unsuspend($name));
		$this->assertTrue($db->delete($name));

		$this->assertTrue($db->add('a', 'ua', self::password()));
		$this->assertTrue($db->add('b', 'ub', self::password()));
		$this->assertTrue($db->deleteDatabases());
		$this->assertSame([], $db->listDatabases());

		$this->expectException(ProcessException::class);
		$this->expectExceptionCode(ResponseCode::E_NOTEXIST);
		$db->delete($name);
	}
}
