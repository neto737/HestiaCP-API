<?php

declare(strict_types=1);

namespace neto737\HestiaCP\Command;

class ResponseCode {

	public const int OK = 0;
	public const int E_ARGS = 1;
	public const int E_INVALID = 2;
	public const int E_NOTEXIST = 3;
	public const int E_EXISTS = 4;
	public const int E_SUSPENDED = 5;
	public const int E_UNSUSPENDED = 6;
	public const int E_INUSE = 7;
	public const int E_LIMIT = 8;
	public const int E_PASSWORD = 9;
	public const int E_FORBIDDEN = 10;
	public const int E_DISABLED = 11;
	public const int E_PARSING = 12;
	public const int E_DISK = 13;
	public const int E_LA = 14;
	public const int E_CONNECT = 15;
	public const int E_FTP = 16;
	public const int E_DB = 17;
	public const int E_RRD = 18;
	public const int E_UPDATE = 19;
	public const int E_RESTART = 20;
	// HestiaCP uses 21 for both: E_BACKUP in the CLI scripts, E_API_DISABLED in the API
	public const int E_BACKUP = 21;
	public const int E_API_DISABLED = 21;

	/** @var array<int, string> */
	private const array MESSAGES = [
		self::OK => 'Command has been successfully performed',
		self::E_ARGS => 'Not enough arguments provided',
		self::E_INVALID => 'Object or argument is not valid',
		self::E_NOTEXIST => 'Object doesn\'t exist',
		self::E_EXISTS => 'Object already exists',
		self::E_SUSPENDED => 'Object is suspended',
		self::E_UNSUSPENDED => 'Object is already unsuspended',
		self::E_INUSE => 'Object can\'t be deleted because is used by the other object',
		self::E_LIMIT => 'Object cannot be created because of hosting package limits',
		self::E_PASSWORD => 'Wrong password',
		self::E_FORBIDDEN => 'Object cannot be accessed by the user',
		self::E_DISABLED => 'Subsystem is disabled',
		self::E_PARSING => 'Configuration is broken',
		self::E_DISK => 'Not enough disk space to complete the action',
		self::E_LA => 'Server is too busy to complete the action',
		self::E_CONNECT => 'Connection failed. Host is unreachable',
		self::E_FTP => 'FTP server is not responding',
		self::E_DB => 'Database server is not responding',
		self::E_RRD => 'RRDtool failed to update the database',
		self::E_UPDATE => 'Update operation failed',
		self::E_RESTART => 'Service restart failed',
		self::E_BACKUP => 'Backup failed, or API access is disabled for this user'
	];

	public static function getMessage(int $code): string {
		return self::MESSAGES[$code] ?? 'Unknown error (exit code ' . $code . ')';
	}
}
