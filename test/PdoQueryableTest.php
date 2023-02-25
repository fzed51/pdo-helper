<?php
/** @noinspection SqlResolve */

/** @noinspection SqlNoDataSourceInspection */

namespace Test;

use Helper\PdoQueryable;
use PDO;
use PDOStatement;
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
     * @return object
     */
    protected function getInstancePdoQueryable(): object
    {
        return new class {
            use PdoQueryable {
                setReqSql as TsetReqSql;
                fetchAll as TfetchAll;
                execute as Texecute;
                fetchOne as TfetchOne;
                getDbDriver as TgetDbDriver;
            }

            public function setReqSql(string $reqSql): void
            {
                $this->TsetReqSql($reqSql);
            }

            public function __construct()
            {
                $pdo = new PDO('sqlite::memory:');
                $pdo->exec("CREATE TABLE test (id INTEGER, data TEXT )");
                $pdo->exec("INSERT INTO test (id, data) VALUES (1, 'a')");
                $pdo->exec("INSERT INTO test (id, data) VALUES (2, 'b')");
                $this->pdo = $pdo;
            }

            /**
             * Retourne tous les enregistrements
             * @param array<string|int,mixed> $param
             * @return array<string,mixed>
             */
            public function fetchAll(array $param = []): array
            {
                return $this->TfetchAll($param);
            }

            /**
             * Execute la requête SQL
             * @param array<mixed> $params
             * @return PDOStatement
             */
            public function execute(array $params): PDOStatement
            {
                return $this->Texecute($params);
            }

            /**
             * Retourne un enregistrement
             * @param array<string|int,mixed> $param
             * @return array<string,mixed>|null
             */
            public function fetchOne(array $param = []): ?array
            {
                return $this->TfetchOne($param);
            }

            public function getDbDriver(): string
            {
                return $this->TgetDbDriver();
            }
        };
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

    /** test de getDbDriver */
    public function testGetDbDriver(): void
    {
        $o = $this->getInstancePdoQueryable();
        self::assertEquals("sqlite", $o->getDbDriver());
    }
}
