<?php

/** @noinspection SqlResolve */

/** @noinspection SqlNoDataSourceInspection */

namespace Test;

use Test\Stub\PdoQueryableStub;
use PHPUnit\Framework\TestCase;

class PdoQueryableTest extends TestCase
{
    /** test de fetchAll */
    public function testFetchAll(): void
    {
        $o = $this->getInstancePdoQueryable();
        $o->setReqSql('select * from test');
        $res = $o->fetchAll();
        self::assertEquals([
            ['id' => 1, 'data' => 'a'],
            ['id' => 2, 'data' => 'b']
        ], $res);
    }


    /**
     * Retourne une instance exposant les methode de PdoQueryable
     */
    protected function getInstancePdoQueryable(): PdoQueryableStub
    {
        return new PdoQueryableStub();
    }

    /** test de execute */
    public function testExecute(): void
    {
        $o = $this->getInstancePdoQueryable();
        $o->setReqSql("INSERT INTO test (id, data) VALUES (?, ?)");
        $res = $o->execute([3, 'b']);
        $o->setReqSql('select count(*) as nb from test');
        $res = $o->fetchOne();
        self::assertEquals(['nb' => 3], $res);
    }

    /** test de fetchOne */
    public function testFetchOne(): void
    {
        $o = $this->getInstancePdoQueryable();
        $o->setReqSql('select * from test');
        $res = $o->fetchOne();
        self::assertEquals(['id' => 1, 'data' => 'a'], $res);
    }

    /** test de getLastReqInfo */
    public function testGetLastReqInfo(): void
    {
        $o = $this->getInstancePdoQueryable();
        self::assertNull($o->getLastReqInfo());
        $o->setReqSql("select *
  from test
  where id = ?");
        $o->fetchOne([2]);
        self::assertSame(
            ['request' => 'select * from test where id = ?', 'params' => [2]],
            $o->getLastReqInfo()
        );
    }

    /** test de getDbDriver */
    public function testGetDbDriver(): void
    {
        $o = $this->getInstancePdoQueryable();
        self::assertEquals("sqlite", $o->getDbDriver());
    }
}
