<?php

/** @noinspection SqlResolve */
/** @noinspection SqlNoDataSourceInspection */
declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 23/06/2020
 * Time: 10:16
 */

namespace Test;

use Helper\PDOFactory;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class PDOFactoryTest extends TestCase
{
    public function testSqlite(): void
    {
        $pdo = PDOFactory::sqlite();
        $this->assertInstanceOf(PDO::class, $pdo);
        $pdo = null;
        touch('./test.db');
        $pdo = PDOFactory::sqlite('./test.db');
        $this->assertInstanceOf(PDO::class, $pdo);
        $pdo = null;
        unlink('./test.db');
    }


    public function testDefaultAttributes(): void
    {
        $pdo = PDOFactory::sqlite();
        self::assertSame(PDO::ERRMODE_EXCEPTION, $pdo->getAttribute(PDO::ATTR_ERRMODE));
        $entity = $this->fetchFirstRow($pdo);
        self::assertEquals((object) ['nom' => 'a'], $entity);
    }

    public function testOverrideCase(): void
    {
        $pdo = PDOFactory::sqlite(':memory:', [PDO::ATTR_CASE => PDO::CASE_UPPER]);
        $entity = $this->fetchFirstRow($pdo);
        self::assertEquals((object) ['NOM' => 'a'], $entity);
    }

    public function testOverrideFetchMode(): void
    {
        $pdo = PDOFactory::sqlite(':memory:', [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
        $entity = $this->fetchFirstRow($pdo);
        self::assertSame(['nom' => 'a'], $entity);
    }

    /**
     * Crée une table `test` avec une colonne `Nom` et retourne sa première ligne
     * selon le fetch mode par défaut de la connexion
     */
    private function fetchFirstRow(PDO $pdo): mixed
    {
        $pdo->exec("CREATE TABLE test (Nom text)");
        $pdo->exec("INSERT INTO test (Nom) VALUES ('a')");
        $stm = $pdo->query("SELECT Nom FROM test");
        self::assertInstanceOf(PDOStatement::class, $stm);
        $entity = $stm->fetch();
        self::assertNotFalse($entity);
        return $entity;
    }

    // TODO : test pour PgSql
    //   private function testPgsql(): void {}

    // TODO : test pour Mysql
    //   private function testMysql(): void {}

    // TODO : test pour Oci
    //  private function testOci(): void {}
}
