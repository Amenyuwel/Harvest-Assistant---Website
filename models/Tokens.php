<?php

class Tokens extends Models{
    public function __construct($table = 'farmer_tokens') {
        parent::__construct($table);
    }
    
    public function token($token){
        $sql = "SELECT farmers.* FROM farmers
        JOIN farmer_tokens ON farmers.id = farmer_tokens.farmer_id
        WHERE remember_token = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$token]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}