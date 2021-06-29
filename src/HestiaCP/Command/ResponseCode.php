<?php

namespace neto737\HestiaCP\Command;

class ResponseCode {

	public const OK = 0;
	public const E_ARGS = 1;
	public const E_INVALID = 2;
	public const E_NOTEXIST = 3;
	public const E_EXISTS = 4;
	public const E_SUSPENDED = 5;
	public const E_UNSUSPENDED = 6;
	public const E_INUSE = 7;
	public const E_LIMIT = 8;
	public const E_PASSWORD = 9;
	public const E_FORBIDEN = 10;
	public const E_DISABLED = 11;
	public const E_PARSING = 12;
	public const E_DISK = 13;
	public const E_LA = 14;
	public const E_CONNECT = 15;
	public const E_FTP = 16;
	public const E_DB = 17;
	public const E_RRD = 18;
	public const E_UPDATE = 19;
	public const E_RESTART = 20;

	public static $messages = [
		self::OK => 'Command has been successfuly performed',
		self::E_ARGS => 'Not enough arguments provided',
		self::E_INVALID => 'Object or argument is not valid',
		self::E_NOTEXIST => 'Object doesn\'t exist',
		self::E_EXISTS => 'Object already exists',
		self::E_SUSPENDED => 'Object is suspended',
		self::E_UNSUSPENDED => 'Object is already unsuspended',
		self::E_INUSE => 'Object can\'t be deleted because is used by the other object',
		self::E_LIMIT => 'Object cannot be created because of hosting package limits',
		self::E_PASSWORD => 'Wrong password',
		self::E_FORBIDEN => 'Object cannot be accessed be the user',
		self::E_DISABLED => 'Subsystem is disabled',
		self::E_PARSING => 'Configuration is broken',
		self::E_DISK => 'Not enough disk space to complete the action',
		self::E_LA => 'Server is to busy to complete the action',
		self::E_CONNECT => 'Connection failed. Host is unreachable',
		self::E_FTP => 'FTP server is not responding',
		self::E_DB => 'Database server is not responding',
		self::E_RRD => 'RRDtool failed to update the database',
		self::E_UPDATE => 'Update operation failed',
		self::E_RESTART => 'Service restart failed'
	];
}
