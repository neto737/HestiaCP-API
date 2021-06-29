# HestiaCP PHP API


## How to use

1) Installation
	```sh
	$ composer require neto737/hestiacp-api
	```
2) Create Client

	a) Easy way
	```php
	use neto737\HestiaCP\Client;
	
	// easy way to create Client
	$client = Client::simpleFactory('https://someHost', 'someUser', 'somePass');
	```
	
	b) For some reasons (more hosts, etc) you may need create objects alone
	```php
	use neto737\HestiaCP\Client;
	use neto737\HestiaCP\Authorization\Credentials;
	use neto737\HestiaCP\Authorization\Host;
	
	$credentials = new Credentials('someUser', 'somePassword');
	$host = new Host('https://someHost', $credentials);
	$client = new Client($host);
	```
3) Usage
	```php
	// verify login
	$client->testAuthorization(); // bool
	```
	You can simply send one of prepared commands (or you can write own command - must implements `\neto737\HestiaCP\Command\ICommand` )
	```php
	$command = new SomeCommand(); 
	$response = $client->send( $command );

	echo $response->getResponseText();
	```
	Or you can use prepared modules
	
	a) user module
	
	```php
	$userModule = $client->getModuleUser();

	$userModule->list(); // returns all users with data
	$userModule->detail('admin'); // returns selected user with data
	$userModule->changePassword('admin', 'otherPa$$word');
	$userModule->add('other_user', 'pa$$word', 'some@email.com');
	$userModule->delete('other_user');
	// ... etc
	```
	
	b) web module

	```php
	$webModule = $client->getModuleWeb('admin'); // web module needs user

	$webModule->listDomains();
	$webModule->addDomain('domain.com');
	$webModule->addDomainLetsEncrypt('domain.com', 'www.domain.com'); // needs longer timeout
	$webModule->deleteDomainLetsEncrypt('domain.com');
	$webModule->addDomainFtp('domain.com', 'test', 'pa$$word');
	$webModule->changeDomainFtpPassword('domain.com', 'admin_test', 'otherPa$$word');
	$webModule->changeDomainFtpPath('domain.com', 'admin_test', 'path/other');
	$webModule->deleteDomainFtp('domain.com', 'admin_test');
	$webModule->deleteDomain('domain.com');
	// ... etc
	```
	
	c) mail module

	```php
	$mailModule = $client->getModuleMail('admin'); // mail module needs user

	$mailModule->listDomains(); // returns mail domains from selected user
	$mailModule->addDomain('domain.com'); // add domain
	$mailModule->listAccounts('domain.com'); // returns accounts from selected user and domain
	$mailModule->listDomainDkim('domain.com'); 
	$mailModule->listDomainDkimDns('domain.com');
	$mailModule->addAccount('domain.com', 'info', 'pa$$word'); // add info@domain.com account
	$mailModule->changeAccountPassword('domain.com', 'info', 'otherPa$$word'); // change info@domain.com password
	$mailModule->deleteAccount('domain.com', 'info');
	$mailModule->deleteDomain('domain.com');
	// ... etc
	```
	
	d) db module

	```php
	// modules
	$dbModule = $client->getModuleDb(); 

	// todo
	// ... etc
	```
	
	e) cron module

	```php
	$cronModule = $client->getModuleCron(); 

	// todo
	// ... etc
	```
	
	f) backup module

	```php
	$backupModule = $client->getModuleBackup(); 

	// todo
	// ... etc
	```