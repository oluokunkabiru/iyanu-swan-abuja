<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\DB;

/**
 * This box has no pdo_sqlite driver, so the default in-memory sqlite test
 * connection is unavailable. Run tests against the real mysql dev database
 * instead, wrapped in a transaction that is always rolled back so nothing
 * persists.
 */
trait UsesMysqlInTransaction
{
    protected function setUp(): void
    {
        parent::setUp();

        DB::purge('mysql');
        config(['database.connections.mysql.database' => 'iyanu_swan']);
        DB::setDefaultConnection('mysql');
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::rollBack();

        parent::tearDown();
    }
}
