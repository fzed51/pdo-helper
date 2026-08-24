<?php
declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 18/06/2020
 * Time: 23:24
 */

namespace Helper;

/**
 * Class SqlRequest
 * générateur de requete sql avec une ecriture sous forme objet
 * @package App
 */
class SqlRequest
{

    /**
     * liste des champs du select
     * @var string[]
     */
    protected $select;

    /**
     * liste des tables
     * @var string[]
     */
    protected $from;
    /**
     * lest des clauses where
     * @var string[]
     */
    protected $where;

    /**
     * SqlRequest constructor.
     */
    public function __construct()
    {
        $this->select = [];
        $this->from = [];
        $this->where = [];
    }

    /**
     * @param string|string[] $select
     * @return SqlRequest
     */
    public function select(string|array $select): SqlRequest
    {
        if (is_string($select)) {
            $select = [$select];
        }
        foreach ($select as $name => $fullname) {
            if (is_int($name)) {
                $this->select[] = $fullname;
            } else {
                $this->select[] = "$fullname as $name";
            }
        }
        return $this;
    }

    /**
     * ajoute un champ au select
     * @param string $fullname
     * @param string $alias
     * @return SqlRequest
     */
    public function addSelect(string $fullname, string $alias = ""): SqlRequest
    {
        $select = empty($alias) ? $fullname : "$fullname as $alias";
        $this->select[] = $select;
        return $this;
    }

    /**
     * retourne la requete sql
     * @return string
     */
    public function sql(): string
    {
        $select = implode(', ', $this->select);
        $from = implode(' ', $this->from);
        $sql = "select {$select} from {$from}";
        if (!empty($this->where)) {
            $where = implode(' and ', $this->where);
            $sql .= " where $where";
        }
        return $sql;
    }

    /**
     * @param string|string[] $from
     * @return SqlRequest
     */
    public function from(string|array $from): SqlRequest
    {
        if (is_string($from)) {
            $from = [$from];
        }
        $this->from = $from;
        return $this;
    }

    /**
     * @param string|string[] $where
     * @return SqlRequest
     */
    public function where(string|array $where): SqlRequest
    {
        if (is_string($where)) {
            $where = ["($where)"];
        }
        $this->where = $where;
        return $this;
    }

    /**
     * ajoute une clause where
     * @param string $where
     * @return SqlRequest
     */
    public function addWhere(string $where): SqlRequest
    {
        $this->where[] = $where;
        return $this;
    }
}
