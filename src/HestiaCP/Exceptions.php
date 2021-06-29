<?php

namespace neto737\HestiaCP;

class Exception extends \Exception { }

class InvalidArgumentException extends Exception { }

class RuntimeException extends Exception { }

class ProcessException extends RuntimeException { }

class ClientException extends RuntimeException {  }

class InvalidResponseException extends ProcessException {  }

class AuthorizationException extends ProcessException {  }
