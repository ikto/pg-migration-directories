# Pg Migration Directories

## Short description

This library is inspired by [DBIx::Migration::Directories](http://search.cpan.org/~crakrjack/DBIx-Migration-Directories-0.12/) perl module.
Main goal is providing tiny platform to run database migrations on PostgreSQL databases.
This library cannot work out of the box though.
To make it work need to implement database connection adapter.
Also, it is possible to alter the behaviour of this tool.

## Features

- Installing database schema from scratch (into empty database);
- Execution of SQL files to upgrade/downgrade database schemas;
- Tracking database schema version;
- Opportunity to choose any way to connect to database (by implementing connection adapter) which makes its integration more consistent.

## Requirements (environment)

- PHP 7.0 or higher

## How it works: high level description

In short the workflow can be illustration as several steps.

### Step 1. Discovering available migrations

At first the tool gets a list of available migrations.
The way how exactly how does it happen can be any.
The exact way of discovering and loading migrations is "described" in migrations discovery implementation.
This package contains the simplest one: SqlFilesDiscovery.

#### SqlFilesDiscovery

For this discovery each migration is a directory which contains a bunch of SQL files.
Migrations directory should have the following layout:

```
DBSCHEMANAME/
 Pg/
  00000001/
  00000001-00000002/
  00000002-00000001/
  00000002-00000003/
  00000003-00000002/
```

At the top level there are directories named as db schema which we manage: in this example we manage the schema named **DBSCHEMANAME**.
There is a **Pg** directory inside of each.
On the next level there is a set of directories named using the following pattern: *[from_version]*-*[to_version]*.
Each of these directories represent the single migration.
In other words each directory contains instructions how to update the db schema from one version to another (it may be upgrade or downgrade).
If directory name contains only one version number - it will be considered as *0*-*[version]*.
Zero version number means that db schema is not installed yet.
Each version-named directory should contain a set of SQL files.
The naming of SQL files is arbitrary, but please note, when performing migration SQL files will be sorted alphabetically.

### Step 2. Building migration path

This step is pretty simple.
The existing db schema already has some version (zero if it is not installed yet).
User/developer provides the version to migrate to (desired version).
By having a list discovered migrations, current and desired versions the migration path builder constructs the list of exact migrations which need to be applied to reach the desired version.
Usually there is no need to alter this behaviour, but it is possible though.

### Step 3. Applying migrations

Even simpler step.
One thing left: apply each of migration to db.
From step 2 we have a list, so here we just iterate over the list and apply each migration.

Each migration is applied with processor.
The migration discovered by SqlFilesDiscovery will be applied by SqlFilesProcessor.
It sorts SQL files alphabetically, loads SQL commands and executed them in order.

## Code example

This library does not provide ready-to-use program to apply migrations.
The following example shows what the program should do.

```php
use IKTO\PgMigrationDirectories\Database\DefaultManagedDatabase;
use IKTO\PgMigrationDirectories\Processor\DefaultProcessorFactory;
use IKTO\PgMigrationDirectories\Discovery\SqlFilesDiscovery;
use IKTO\PgMigrationDirectories\MigrationPathBuilder\MigrationPathBuilder;

/**
 * Step 0. Creating managed db object.
 */

// Here we create a connection adapter. This is just example and won't work of course.
$connection_adapter = new ConnectionAdapterInterface();
// Creating managed db.
$migration_db = new DefaultManagedDatabase($connection_adapter, 'DBSCHEMANAME', 'public');
// Setting processor factory.
// Processor factory is responsible for providing correct processor for migration.
// The DefaultProcessorFactory is shipped with this package and can be used
// if you don't create new types of migration definitions.
$migration_db->setProcessorFactory(new DefaultProcessorFactory());
// Specifying target db version. In real app it will come from config or something like this.
// This does not have real leverage and used just to be able to get this value later.
$migration_db->setDesiredVersion(42);

/**
 * Step 1. Discovering available migrations
 */
// Instantiating migrations discovery.
// This does not do real discovery. Real discovery will be triggered on the next step.
$discovery = new SqlFilesDiscovery(__DIR__ . '/sql/migrations', 'DBSCHEMANAME');

/**
 * Step 2. Building the migration path.
 */
// Retrieving current version number.
$startingVersion = $migration_db->getCurrentVersion();
// Instantiating migration path builder.
$builder = new MigrationPathBuilder($discovery);
// Creating migration path.
// Here we get desired version which we can in Step 0.
$path = $builder->getMigrationPath($startingVersion, $migration_db->getDesiredVersion());

/**
 * Step 3. Applying migration (choose one of two options here).
 */

// Applying migration path to the database (each step in separate transaction).
foreach ($path as $migration) {
    $migration_db->openTransaction();
    $migration_db->applyMigration($migration);
    $migration_db->commitTransaction();
    printf('Migrated from %d to %d', $migration->getStartingVersion(), $migration->getTargetVersion());
}

// Applying migration path to the database (whole migration is single transaction).
$migration_db->openTransaction();
foreach ($path as $migration) {
    $migration_db->applyMigration($migration);
    printf('Migrated from %d to %d', $migration->getStartingVersion(), $migration->getTargetVersion());
}
$migration_db->commitTransaction();
```

## Tracking db schema version

To monitor the state of the db the library holds the data about migration inside of db.
These table should be created with the first migration which install the db schema.

```sql
CREATE TABLE migration_schema_version (
    name character varying(128) NOT NULL,
    version real NOT NULL,
    CONSTRAINT migration_schema_version_pkey PRIMARY KEY (name)
);
```

```sql
CREATE TABLE migration_schema_log (
    id serial NOT NULL,
    schema_name character varying(128) NOT NULL,
    event_time timestamp with time zone DEFAULT now() NOT NULL,
    old_version real DEFAULT 0 NOT NULL,
    new_version real NOT NULL,
    CONSTRAINT migration_schema_log_pkey PRIMARY KEY (id),
    CONSTRAINT migration_schema_log_schema_name_fkey FOREIGN KEY (schema_name)
        REFERENCES migration_schema_version (name) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);
```

Usually these tables are stored under the **public** schema.
But you are able to store them in another, just don't forget to change the third constructor argument when you're creating managed db object (or corresponding parameter for StateManager).

Also, it is possible to store db schema version somehow differently, but then StateManager needs to be replaced. To replace StateManager the managed db class should be replaced as well.
