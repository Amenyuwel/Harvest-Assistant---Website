<?php

class Harvest extends Models
{

    public function __construct($table = 'harvests') {
        parent::__construct($table);
    }

    public function harvests()
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                farmers.rsbsa_num AS rsbsa_number, 
                farmers.first_name, 
                farmers.last_name, 
                crops.crop_name, 
                barangay.barangay_name AS barangay,  
                harvests.date_harvested,  
                harvests.estimated_produce, 
                harvests.estimated_income,
                harvests.severity  -- Include the severity column here
            FROM `harvests` 
            INNER JOIN farmers ON harvests.farmer_id = farmers.id
            INNER JOIN crops ON harvests.crop_id = crops.id
            INNER JOIN barangay ON farmers.barangay_id = barangay.id 
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getSeverityByFarmerId($farmer_id) {
        // Prepare the SQL query to fetch the severity
        $sql = "SELECT severity FROM {$this->table} WHERE farmer_id = ? AND is_active = 1";
        
        // Prepare and execute the statement
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$farmer_id]);
        
        // Fetch the severity value
        return $stmt->fetchColumn(); // This returns the first column of the first row
    }
    
}


