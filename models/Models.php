<?php
class Models
{
    protected $pdo;
    protected $table;
    protected $stmt;
    protected $params;

    public function __construct($table)
    {
        $this->table = $table;
        $this->pdo = new PDO("mysql:host=localhost;dbname=u766798681_dbHarvest", "u766798681_adminHarvest", "EmmanBayot_69");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function id($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function all()
    {

        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function where($conditions = [])
    {
        $sql = $this->stmt ?? "SELECT * FROM {$this->table}";

        if (!empty($conditions)) {
            $sql .= " WHERE";
            $params = [];
            $isFirstCondition = true;

            foreach ($conditions as $column => $value) {
                if (!$isFirstCondition) {
                    $sql .= " AND";
                }

                if (is_array($value) && strtolower($value[0]) === 'like') {
                    // Handle LIKE condition
                    $sql .= " {$column} LIKE ?";
                    $params[] = $value[1];
                } else {
                    // Handle equality condition
                    $sql .= " {$column} = ?";
                    $params[] = $value;
                }

                $isFirstCondition = false;
            }
            $this->params = $params;
        }

        $this->stmt .= $sql;
        return $this;
    }

    public function get(){
        $stmt = $this->pdo->prepare($this->stmt);
        $stmt->execute($this->params ?? []);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
