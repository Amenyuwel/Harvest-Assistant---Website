<?php

class PestReports extends Models
{
    public function __construct($table = 'pest_report') {
        parent::__construct($table);
    }

    // Method to get pest reports based on the farmer's full name
    public function getReportByFarmerName($firstName, $lastName)
    {
        $fullName = $firstName . ' ' . $lastName; // Concatenate first and last name
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM `pest_report`
            WHERE `name` = :fullName
        ");
        $stmt->execute(['fullName' => $fullName]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>