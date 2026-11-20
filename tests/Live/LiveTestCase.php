<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Tests\Live;

use neto737\HestiaCP\Client;
use neto737\HestiaCP\Command\MakeTmpFile;
use neto737\HestiaCP\Command\RawListCommand;

/**
 * Runs against a real HestiaCP server. Skipped unless these environment variables are set:
 *
 *  - HESTIACP_HOST        https://panel.example.com
 *  - HESTIACP_ACCESS_KEY  an admin access key, ACCESS_KEY:SECRET_KEY
 *  - HESTIACP_HASH        optional, a legacy API key, for the legacy endpoint tests
 *  - HESTIACP_PASSWORD    optional, the admin password, for the legacy endpoint tests
 *  - HESTIACP_VERIFY      optional, a CA bundle path, or 0 to turn TLS verification off
 *
 * Each test class creates its own user and deletes it, with everything it owns, afterwards.
 * The tests never send wrong credentials, so they can't get the calling IP banned by Fail2Ban.
 */
abstract class LiveTestCase extends \PHPUnit\Framework\TestCase {

	protected static Client $client;

	protected static ?string $user = null;

	public static function setUpBeforeClass(): void {
		$key = getenv('HESTIACP_ACCESS_KEY');
		if (getenv('HESTIACP_HOST') === false || $key === false) {
			self::markTestSkipped('Set HESTIACP_HOST and HESTIACP_ACCESS_KEY to run the live tests');
		}

		static::$client = self::createClient($key);
		static::$user = 'apitest' . bin2hex(random_bytes(3));
		static::$client->getModuleUser()->add(static::$user, self::password(), static::$user . '@example.net');
	}

	public static function tearDownAfterClass(): void {
		if (static::$user !== null) {
			static::$client->getModuleUser()->delete(static::$user);
			static::$user = null;
		}
	}

	protected static function createClient(string $user, ?string $password = null): Client {
		$verify = getenv('HESTIACP_VERIFY');

		return Client::simpleFactory((string) getenv('HESTIACP_HOST'), $user, $password, [
			'timeout' => 60,
			'verify' => $verify === false ? true : ($verify === '0' ? false : $verify)
		]);
	}

	protected static function user(): string {
		return (string) static::$user;
	}

	protected static function password(): string {
		return 'Pw-' . bin2hex(random_bytes(8));
	}

	/**
	 * A domain unique to this test class, under the reserved .example TLD.
	 */
	protected static function domain(string $prefix): string {
		return $prefix . '.' . self::user() . '.example';
	}

	/**
	 * The server's first IP address, for the web and DNS domains.
	 */
	protected static function ip(): string {
		$ips = static::$client->send(new RawListCommand('v-list-sys-ips', ['json']));

		return (string) array_key_first($ips);
	}

	/**
	 * Writes a new self-signed certificate and its key to /tmp/<domain>.crt and /tmp/<domain>.key on the server,
	 * where v-add-web-domain-ssl and v-add-mail-domain-ssl read them. HestiaCP's web UI also uploads them to /tmp.
	 *
	 * @return string the certificate
	 */
	protected static function uploadCertificate(string $domain): string {
		// PHP on Windows ships without an openssl.cnf
		$config = (string) tempnam(sys_get_temp_dir(), 'cnf');
		file_put_contents($config, "[req]\ndistinguished_name = dn\n[dn]\n");
		$options = ['config' => $config, 'private_key_type' => OPENSSL_KEYTYPE_RSA, 'private_key_bits' => 2048, 'digest_alg' => 'sha256'];

		try {
			$key = openssl_pkey_new($options);
			self::assertNotFalse($key);
			$csr = openssl_csr_new(['commonName' => $domain], $key, $options);
			self::assertNotFalse($csr);
			self::assertNotTrue($csr);
			$certificate = openssl_csr_sign($csr, null, $key, 30, $options, random_int(1, PHP_INT_MAX));
			self::assertNotFalse($certificate);
			self::assertTrue(openssl_x509_export($certificate, $crt));
			self::assertTrue(openssl_pkey_export($key, $keyPem, null, $options));
		} finally {
			unlink($config);
		}

		static::$client->send(new MakeTmpFile($domain . '.crt', $crt));
		static::$client->send(new MakeTmpFile($domain . '.key', $keyPem));

		return $crt;
	}

	/**
	 * Empties the files uploadCertificate() wrote, since v-make-tmp-file leaves them behind.
	 */
	protected static function clearCertificate(string $domain): void {
		static::$client->send(new MakeTmpFile($domain . '.crt', ''));
		static::$client->send(new MakeTmpFile($domain . '.key', ''));
	}
}
