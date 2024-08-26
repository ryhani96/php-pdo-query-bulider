<?php

class QueryBuilder
{
    protected $pdo;
    protected $table;
    protected $OrderBy;
    protected $columns = ['*'];
    protected $wheres = [];
    protected $values = [];
    protected $method;

    private $LIMIT = "";
    public const OR = 'OR', AND = 'AND';
    public const ASC  = "ASC", DESC = "DESC";
    const EQUALS="=",GREATER=">",SMALLER="<",NOT_EQUAL="<>";
    

    public function __construct(Database $pdo)
    {
        $this->pdo = $pdo;
    }

    public function OrderBy($par, string $order = "ASC")
    {
        $this->OrderBy = ' ORDER BY ' . $par . " " . strtoupper($order);
        return $this;
    }
    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function select(...$columns)
    {
        $this->columns = $columns;
        return $this;
    }
    function dataFilter($par)
    {
        $par = htmlentities($par);
        $par = trim($par);
        $par = strip_tags($par);
        $par = htmlspecialchars($par);
        return $par;
    }
    public function where($column, $operator, $value, $boolean = QueryBuilder::AND)
    {
        $this->wheres[] = compact('column', 'operator', 'value', 'boolean');
        return $this;
    }
    public function limit(int $limit, int $offset = null): self
    {
        if ($offset === null) {
            $this->LIMIT = (" LIMIT $limit");
        } else {
            $this->LIMIT = (" LIMIT $limit OFFSET $offset");
        }

        return $this;
    }
    public function insert(array $values)
    {
        $this->method = 'insert';
        $this->values = $values;
        return $this;
    }

    public function update(array $values)
    {
        $this->method = 'update';
        $this->values = $values;
        return $this;
    }
    public function  delete()
    {
        $this->method = 'delete';

        return  $this;
    }

    public function buildDelete()
    {
        $query = "DELETE FROM " . $this->table;
        if (!empty($this->wheres)) {
            $query .= " WHERE ";
            foreach ($this->wheres as $where) {
                $query .= "{$where['column']} {$where['operator']} ? {$where['boolean']} ";
            }
            $query = rtrim($query, ' AND ');
        }
        return $query;
    }
    public function buildSelect()
    {
        $query = "SELECT " . implode(', ', $this->columns) . " FROM $this->table";

        if (!empty($this->wheres)) {
            $query .= " WHERE ";
            foreach ($this->wheres as $where) {


                $query .= "{$where['column']} {$where['operator']} ? {$where['boolean']} ";
            }
        }
        $query = rtrim($query, ' AND ');
        // Order By
        if (isset($this->OrderBy)) {
            $query .= $this->OrderBy;
        }
        if ($this->LIMIT != "") {
            $query .= $this->LIMIT;
        }

        return $query;
    }

    public function buildInsert()
    {
        $columns = implode(', ', array_keys($this->values));
        $placeholders = rtrim(str_repeat('?, ', count($this->values)), ', ');

        return "INSERT INTO $this->table ($columns) VALUES ($placeholders)";
    }

    public function buildUpdate()
    {
        $set = '';
        foreach ($this->values as $column => $value) {
            $set .= "$column = ?, ";
        }
        $set = rtrim($set, ', ');

        $query = "UPDATE $this->table SET $set";

        if (!empty($this->wheres)) {
            $query .= " WHERE ";
            foreach ($this->wheres as $where) {
                $query .= "{$where['column']} {$where['operator']} ? {$where['boolean']} ";
            }
            $query = rtrim($query, ' AND ');
        }

        return $query;
    }

    public function execute()
    {
        if ($this->method == "insert") {
            $statement = $this->pdo->prepare($this->buildInsert());

            $statement->execute(array_values($this->values));
            return $this->pdo->lastInsertId();
        } else if ($this->method == "update") {

            $statement = $this->pdo->prepare($this->buildUpdate());
            $parameters = array_merge(array_values($this->values), array_map(function ($where) {
                return $where['value'];
            }, $this->wheres));

            $statement->execute($parameters);
            return $statement->rowCount();
        } else if ($this->method == "delete") {
            $statement = $this->pdo->prepare($this->buildDelete());
            $parameters = array_merge(array_values($this->values), array_map(function ($where) {
                return $where['value'];
            }, $this->wheres));

            $statement->execute($parameters);
        }
    }
    /** * @author ryhani96 */
    function cleanMe($par)
    {
        $par = rtrim($par, " ");
        $par = htmlspecialchars($par);
        return $par;
    }
    public function get()
    {

        $statement = $this->pdo->prepare($this->buildSelect());
        $parameters = array_map(function ($where) {
            return $where['value'];
        }, $this->wheres);
        // echo $this->buildSelect();
        // echo json_encode($parameters);
        $statement->execute($parameters);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
}
