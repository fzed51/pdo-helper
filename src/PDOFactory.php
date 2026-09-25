<?php

namespace Helper;

use PDO;

/**
 * PDOFactory est une collection de methode statique facilitant la création de connecteur PDO
 * @author Fabien Sanchez
 */
class PDOFactory
{
    /**
     * Attributs appliqués par défaut à chaque connecteur, surchargeables via $attributes
     * @var array<int, mixed>
     */
    private const DEFAULT_ATTRIBUTES = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_CASE => PDO::CASE_LOWER,
    ];

    /**
     * Applique les attributs par défaut, surchargés par $attributes
     *
     * @param PDO $pdo
     * @param array<int, mixed> $attributes
     * @return PDO
     */
    private static function configPdo(PDO $pdo, array $attributes): PDO
    {
        foreach ($attributes + self::DEFAULT_ATTRIBUTES as $attribute => $value) {
            $pdo->setAttribute($attribute, $value);
        }
        return $pdo;
    }

    /**
     * Connecteur Mysql
     *
     * @param string $host     localhost / adresse IP
     * @param string $dbName   Nom de la base de donnée
     * @param string $username User de la base mysql
     * @param string $password Mot de passe
     * @param integer $port    Numero de port (defaut : 3306)
     * @param string $charset  Charset utilisé (defaut : utf8)
     * @param array<int, mixed> $attributes Attributs PDO surchargeant les défauts
     * @return PDO
     */
    public static function mysql(
        string $host,
        string $dbName,
        string $username,
        string $password,
        int $port = 3306,
        string $charset = 'utf8',
        array $attributes = []
    ): PDO {
        $dns = "mysql:host=$host;port=$port;dbname=$dbName;charset=$charset";
        // $options = array(
        //     PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES $charset",
        // );
        $pdo = new PDO($dns, $username, $password);
        return self::configPdo($pdo, $attributes);
    }

    /**
     * Connecteur sqlite
     *
     * @param string $filename Chemin/de/la/base/de/donnee
     * @param array<int, mixed> $attributes Attributs PDO surchargeant les défauts
     * @return PDO
     */
    public static function sqlite(string $filename = ':memory:', array $attributes = []): PDO
    {
        if ($filename !== ':memory:') {
            if (!file_exists($filename)) {
                throw new \InvalidArgumentException("Le fichier $filename n'a pas été trouvé! ");
            }
            $filename = realpath($filename);
        }
        $pdo = new PDO('sqlite:' . $filename);
        return self::configPdo($pdo, $attributes);
    }

    /**
     * Connecteur Oracle
     *
     * @param string $sid      SID enregistré dans le TNSNAME
     * @param string $user     User oracle
     * @param string $password Mot de passe du user oracle
     * @param string $charset  Charset de la connexion (optionnel)
     * @param array<int, mixed> $attributes Attributs PDO surchargeant les défauts
     * @return PDO
     */
    public static function oci(
        string $sid,
        string $user,
        string $password,
        string $charset = '',
        array $attributes = []
    ): PDO {
        if (!empty($charset)) {
            $charset = ';charset=' . $charset;
        }
        $pdo = new PDO("oci:dbname=$sid$charset", $user, $password);
        $pdo = self::configPdo($pdo, $attributes);
        try {
            $pdo->exec("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
            $pdo->exec("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
        } catch (\PDOException $ex) {
            throw new \RuntimeException("Impossible de modifier les formats de date de la base", 0, $ex);
        }
        return $pdo;
    }

    /**
     * Connecteur Postgresql
     * @param string $dbname Nom de la base de donnée
     * @param string $host Adresse du serveur
     * @param string $user User postgres
     * @param string $password Mot de passe du user postgres
     * @param int $port = 5432 Port du serveur
     * @param array<int, mixed> $attributes Attributs PDO surchargeant les défauts
     * @return PDO
     */
    public static function pgsql(
        string $dbname,
        string $host,
        string $user,
        string $password,
        int $port = 5432,
        array $attributes = []
    ): PDO {
        $pdo = new PDO("pgsql:dbname=$dbname;port=$port;host=$host", $user, $password);
        return self::configPdo($pdo, $attributes);
    }
}
