<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Command;

use GuzzleHttp\Psr7\Response;
use neto737\HestiaCP\Command\Dns\AddDnsDomain;
use neto737\HestiaCP\Command\Dns\AddDnsRecord;
use neto737\HestiaCP\Command\Dns\ChangeDnsDomainDnssec;
use neto737\HestiaCP\Command\Dns\ChangeDnsDomainExp;
use neto737\HestiaCP\Command\Dns\ChangeDnsDomainIp;
use neto737\HestiaCP\Command\Dns\ChangeDnsDomainSoa;
use neto737\HestiaCP\Command\Dns\ChangeDnsDomainTpl;
use neto737\HestiaCP\Command\Dns\ChangeDnsDomainTtl;
use neto737\HestiaCP\Command\Dns\ChangeDnsRecord;
use neto737\HestiaCP\Command\Dns\ChangeDnsRecordId;
use neto737\HestiaCP\Command\Dns\DeleteDnsDomain;
use neto737\HestiaCP\Command\Dns\DeleteDnsRecord;
use neto737\HestiaCP\Command\Dns\ListDnsDomain;
use neto737\HestiaCP\Command\Dns\ListDnsDomains;
use neto737\HestiaCP\Command\Dns\ListDnsRecords;
use neto737\HestiaCP\Command\Dns\ListDnssecPublicKey;
use neto737\HestiaCP\Command\Dns\ListDnsTemplate;
use neto737\HestiaCP\Command\Dns\ListDnsTemplates;
use neto737\HestiaCP\Command\Dns\SuspendDnsDomain;
use neto737\HestiaCP\Command\Dns\SuspendDnsRecord;
use neto737\HestiaCP\Command\Dns\UnsuspendDnsDomain;
use neto737\HestiaCP\Command\Dns\UnsuspendDnsRecord;
use neto737\HestiaCP\Exceptions\InvalidArgumentException;

class DnsTest extends \PHPUnit\Framework\TestCase {

	public function testListDnsDomains(): void {
		$command = new ListDnsDomains('admin');
		$this->assertSame('v-list-dns-domains', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertFalse($command->needReturnCode());
		$this->assertSame([], $command->process(new Response()));
	}

	public function testDomainDetailCommands(): void {
		foreach ([
			'v-list-dns-domain' => new ListDnsDomain('admin', 'example.com'),
			'v-list-dnssec-public-key' => new ListDnssecPublicKey('admin', 'example.com')
		] as $name => $command) {
			$this->assertSame($name, $command->getName());
			$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'json'], $command->getRequestParams());
			$this->assertFalse($command->needReturnCode());

			try {
				$command->process(new Response());
				$this->fail($name . ' should throw on an empty response');
			} catch (\neto737\HestiaCP\Exceptions\InvalidResponseException) {
			}
		}
	}

	public function testListDnsRecords(): void {
		$command = new ListDnsRecords('admin', 'example.com');
		$this->assertSame('v-list-dns-records', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'json'], $command->getRequestParams());

		$this->assertFalse($command->needReturnCode());
		$this->assertSame([], $command->process(new Response()));
	}

	public function testListDnsTemplates(): void {
		$command = new ListDnsTemplates();
		$this->assertSame('v-list-dns-templates', $command->getName());
		$this->assertSame(['arg1' => 'json'], $command->getRequestParams());

		$command = new ListDnsTemplate('default');
		$this->assertSame('v-list-dns-template', $command->getName());
		$this->assertSame(['arg1' => 'default', 'arg2' => 'json'], $command->getRequestParams());

		$this->assertFalse($command->needReturnCode());
		$this->assertSame([], $command->process(new Response()));
	}

	public function testAddDnsDomain(): void {
		$command = new AddDnsDomain('admin', 'example.com', '192.0.2.10');
		$this->assertSame('v-add-dns-domain', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => '192.0.2.10'], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testAddDnsDomainWithNameservers(): void {
		$command = new AddDnsDomain('admin', 'example.com', '192.0.2.10', ['ns1.example.com', 'ns2.example.com']);
		$this->assertSame(['arg4' => 'ns1.example.com', 'arg5' => 'ns2.example.com'], array_slice($command->getRequestParams(), 3));
	}

	public function testAddDnsDomainRestartAndDnssecAfterNameservers(): void {
		// the unused nameserver slots are sent empty, so restart and DNSSEC keep their positions
		$command = new AddDnsDomain('admin', 'example.com', '192.0.2.10', ['ns1.example.com', 'ns2.example.com'], false, true);
		$this->assertSame([
			'arg4' => 'ns1.example.com',
			'arg5' => 'ns2.example.com',
			'arg6' => '',
			'arg7' => '',
			'arg8' => '',
			'arg9' => '',
			'arg10' => '',
			'arg11' => '',
			'arg12' => 'no',
			'arg13' => 'yes'
		], array_slice($command->getRequestParams(), 3));
	}

	public function testAddDnsDomainSingleNameserver(): void {
		$this->expectException(InvalidArgumentException::class);
		new AddDnsDomain('admin', 'example.com', '192.0.2.10', ['ns1.example.com']);
	}

	public function testAddDnsDomainTooManyNameservers(): void {
		$this->expectException(InvalidArgumentException::class);
		new AddDnsDomain('admin', 'example.com', '192.0.2.10', array_map(fn (int $i) => 'ns' . $i . '.example.com', range(1, 9)));
	}

	public function testDomainCommands(): void {
		foreach ([
			'v-delete-dns-domain' => new DeleteDnsDomain('admin', 'example.com'),
			'v-suspend-dns-domain' => new SuspendDnsDomain('admin', 'example.com'),
			'v-unsuspend-dns-domain' => new UnsuspendDnsDomain('admin', 'example.com')
		] as $name => $command) {
			$this->assertSame($name, $command->getName());
			$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com'], $command->getRequestParams());
			$this->assertTrue($command->needReturnCode());
		}
	}

	public function testChangeDomainCommands(): void {
		foreach ([
			'v-change-dns-domain-ip' => [new ChangeDnsDomainIp('admin', 'example.com', '192.0.2.20'), '192.0.2.20'],
			'v-change-dns-domain-ttl' => [new ChangeDnsDomainTtl('admin', 'example.com', 3600), '3600'],
			'v-change-dns-domain-exp' => [new ChangeDnsDomainExp('admin', 'example.com', '2027-10-18'), '2027-10-18'],
			'v-change-dns-domain-soa' => [new ChangeDnsDomainSoa('admin', 'example.com', 'ns1.example.com'), 'ns1.example.com'],
			'v-change-dns-domain-tpl' => [new ChangeDnsDomainTpl('admin', 'example.com', 'gmail'), 'gmail'],
			'v-change-dns-domain-dnssec' => [new ChangeDnsDomainDnssec('admin', 'example.com', true), 'yes']
		] as $name => [$command, $value]) {
			$this->assertSame($name, $command->getName());
			$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => $value], $command->getRequestParams());
			$this->assertTrue($command->needReturnCode());
		}

		$this->assertSame('no', (new ChangeDnsDomainDnssec('admin', 'example.com', false))->getRequestParams()['arg3']);
	}

	public function testAddDnsRecord(): void {
		$command = new AddDnsRecord('admin', 'example.com', 'www', 'A', '192.0.2.10');
		$this->assertSame('v-add-dns-record', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'example.com',
			'arg3' => 'www',
			'arg4' => 'A',
			'arg5' => '192.0.2.10'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testAddDnsRecordTtlOnly(): void {
		// empty priority and ID make HestiaCP use its default priority and the next free ID
		$command = new AddDnsRecord('admin', 'example.com', 'www', 'A', '192.0.2.10', null, null, null, 300);
		$this->assertSame(['arg6' => '', 'arg7' => '', 'arg8' => '', 'arg9' => '300'], array_slice($command->getRequestParams(), 5));
	}

	public function testChangeDnsRecord(): void {
		$command = new ChangeDnsRecord('admin', 'example.com', 3, '@', 'MX', 'mail.example.com', 20);
		$this->assertSame('v-change-dns-record', $command->getName());
		$this->assertSame([
			'arg1' => 'admin',
			'arg2' => 'example.com',
			'arg3' => '3',
			'arg4' => '@',
			'arg5' => 'MX',
			'arg6' => 'mail.example.com',
			'arg7' => '20'
		], $command->getRequestParams());

		$this->assertTrue($command->needReturnCode());

		$this->expectException(\neto737\HestiaCP\Exceptions\InvalidResponseException::class);
		$command->process(new Response());
	}

	public function testChangeDnsRecordId(): void {
		$command = new ChangeDnsRecordId('admin', 'example.com', 3, 30);
		$this->assertSame('v-change-dns-record-id', $command->getName());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => '3', 'arg4' => '30'], $command->getRequestParams());
		$this->assertTrue($command->needReturnCode());
	}

	public function testRecordCommands(): void {
		foreach ([
			'v-delete-dns-record' => new DeleteDnsRecord('admin', 'example.com', 3),
			'v-suspend-dns-record' => new SuspendDnsRecord('admin', 'example.com', 3),
			'v-unsuspend-dns-record' => new UnsuspendDnsRecord('admin', 'example.com', 3)
		] as $name => $command) {
			$this->assertSame($name, $command->getName());
			$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => '3'], $command->getRequestParams());
			$this->assertTrue($command->needReturnCode());
		}
	}

	public function testRestartIsSentWhenSet(): void {
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => 'yes'], (new DeleteDnsDomain('admin', 'example.com', true))->getRequestParams());
		$this->assertSame(['arg1' => 'admin', 'arg2' => 'example.com', 'arg3' => '3', 'arg4' => 'no'], (new DeleteDnsRecord('admin', 'example.com', 3, false))->getRequestParams());
		$this->assertSame('no', (new ChangeDnsDomainTtl('admin', 'example.com', 3600, false))->getRequestParams()['arg4']);
		$this->assertSame('yes', (new ChangeDnsRecordId('admin', 'example.com', 3, 30, true))->getRequestParams()['arg5']);
	}
}
