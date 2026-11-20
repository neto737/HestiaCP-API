# Databases

```php
$db = $client->getModuleDatabase('admin');
```

The database module works on the databases of one user, given when you get the module.

## Adding

```php
$db->add('database', 'dbuser', 'dbpass'); // creates admin_database with the user admin_dbuser

$db->add('database', 'dbuser', 'dbpass', 'pgsql'); // PostgreSQL, if it is installed on the server

$db->add('database', 'dbuser', 'dbpass', charset: 'LATIN1');       // a character set other than UTF8MB4
$db->add('database', 'dbuser', 'dbpass', host: 'db2.example.com'); // a database server other than the one HestiaCP picks
```

The host must be one of the database servers HestiaCP knows (`v-list-database-hosts`). Without it, HestiaCP picks one, which is `localhost` on a single server.

The type defaults to `mysql`.

## Listing

```php
$db->listDatabases(); // ['admin_database' => ['DATABASE' => '...', 'DBUSER' => '...', ...], ...]

$db->listDatabase('admin_database'); // the fields of one database
```

## Changing the password

```php
$db->changePassword('admin_database', 'newPa$$word'); // the password of the database user
```

## Suspending

```php
$db->suspend('admin_database');
$db->unsuspend('admin_database');
```

## Deleting

```php
$db->delete('admin_database'); // one database

$db->deleteDatabases(); // every database of the user
```

## Name prefixes

HestiaCP prefixes the database name and the database user with the owner's username. `add()` takes the names without the prefix (`database`); the other methods take the full name (`admin_database`).

All methods except the `list*()` methods return `true` on success and throw an exception on failure (see [Error handling](errors.md)).
