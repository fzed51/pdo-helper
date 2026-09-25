<?php

namespace Test\Stub;

use Helper\PdoQueryable;
use PDO;
use PDOStatement;

/**
 * Expose en public les méthodes protégées du trait PdoQueryable pour les tests
 */
class PdoQueryableStub
{
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
}
