# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Run all tests
composer test

# Run a single test file
./vendor/bin/phpunit test/PdoQueryableTest.php

# Run a single test method
./vendor/bin/phpunit --filter testMethodName test/SomeTest.php

# Code style check (PSR-2)
./vendor/bin/phpcs

# Static analysis (level 6)
./vendor/bin/phpstan analyse

# Run both checks together
composer check
```

## Architecture

PHP 8.1+ library under the `Helper\` namespace (PSR-4, in `src/`). Four components:

### `PDOFactory`
Static factory that creates pre-configured PDO connections (`mysql()`, `sqlite()`, `pgsql()`, `oci()`). Class-level statics `$case` and `$fetchMode` set defaults applied to all connections via `configPdo()`.

### `DbQuickUse`
High-level CRUD wrapper around a PDO instance. Provides `select()`, `selectOne()`, `insertInto()`, `update()`, `delete()`, `countElement()`, and helpers `genWhere()` / `genSelect()` for building parameterized SQL fragments. All write operations go through `prepareAndExecute()`.

### `SqlRequest`
Fluent SELECT query builder. Methods (`select()`, `addSelect()`, `from()`, `where()`, `addWhere()`) return `$this` for chaining; `sql()` produces the final SQL string. Supports field aliases and multiple AND-joined WHERE conditions.

### `PdoQueryable` (trait)
Low-level trait for reading/writing via PDO. Caches prepared statements by SHA-256 hash of the SQL. Handles multi-charset encoding conversion (UTF-8, CP1252, ISO-8859-1/15, ASCII) on both input parameters and output rows. Key methods: `fetchAll()`, `fetchOne()`, `execute()`, `getRowsAffected()`. Inject a connection with `setPdo()`.

## Testing approach

Tests use SQLite in-memory databases (`PDOFactory::sqlite(':memory:')`). `PdoQueryableTest` creates a concrete class that uses the trait rather than mocking it.
