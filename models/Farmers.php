<?php

class Farmers extends Models {
    public function __construct($table = 'farmers') {
        parent::__construct($table);
    }

    // Pagination for farmers
    public function farmers($page = 1, $item_per_page = 10) {
        $page = $page <= 0 ? 1 : $page; 
        $offset = ($page - 1) * $item_per_page;

        $sql = "SELECT farmers.id, farmers.rsbsa_num, farmers.first_name, 
                       farmers.middle_name, farmers.last_name, farmers.contact_number, 
                       farmers.area, farmers.crop_id, farmers.barangay_id, 
                       farmers.role_id, barangay.barangay_name, user_levels.role, crops.crop_name 
                FROM farmers
                JOIN crops ON farmers.crop_id = crops.id
                JOIN barangay ON farmers.barangay_id = barangay.id
                JOIN user_levels ON farmers.role_id = user_levels.id
                ORDER BY farmers.rsbsa_num ASC 
                LIMIT $item_per_page
                OFFSET $offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Execute and return results
    }

    // Method to get farmer ID by first and last name
    public function getFarmerIdByName($firstName, $lastName) {
        // Ensure case insensitive and trim the values to remove any whitespaces
        $sql = "SELECT id FROM farmers 
                WHERE TRIM(LOWER(first_name)) = TRIM(LOWER(:firstName)) 
                AND TRIM(LOWER(last_name)) = TRIM(LOWER(:lastName))";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'firstName' => $firstName,
            'lastName' => $lastName,
        ]);

        // Debug to ensure query is being executed properly
        if (!$stmt) {
            echo "Error in SQL execution: " . implode(":", $stmt->errorInfo());
        }

        return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the farmer ID
    }
}
