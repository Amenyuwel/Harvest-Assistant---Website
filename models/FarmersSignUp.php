<?php

class FarmersSignUp extends Models {
    public function __construct($table = 'farmers_signup') {
        parent::__construct($table);    
    }

    // Fetch all farmers with crop and barangay names
    public function all($page = 1, $item_per_page = 10) {
        $page = $page <= 0 ? 1 : $page; 
        $offset = ($page - 1) * $item_per_page;

        // SQL query to fetch farmers with associated crops and barangays
        $sql = "SELECT 
                    farmers_signup.*, 
                    crops.crop_name, 
                    barangay.barangay_name 
                FROM 
                    farmers_signup
                LEFT JOIN 
                    crops ON farmers_signup.crop_id = crops.id
                LEFT JOIN 
                    barangay ON farmers_signup.barangay_id = barangay.id
                ORDER BY 
                    CONCAT(farmers_signup.first_name, farmers_signup.last_name) ASC
                LIMIT :item_per_page OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':item_per_page', $item_per_page, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch only farmers whose is_approve is 0 (Pending farmers)
    public function getPendingFarmers($page = 1, $item_per_page = 10) {
        $page = $page <= 0 ? 1 : $page; 
        $offset = ($page - 1) * $item_per_page;

        // SQL query to fetch pending farmers with associated crops and barangays
        $sql = "SELECT 
                    farmers_signup.*, 
                    crops.crop_name, 
                    barangay.barangay_name 
                FROM 
                    farmers_signup
                LEFT JOIN 
                    crops ON farmers_signup.crop_id = crops.id
                LEFT JOIN 
                    barangay ON farmers_signup.barangay_id = barangay.id
                WHERE 
                    farmers_signup.is_approve = 0
                ORDER BY 
                    CONCAT(farmers_signup.first_name, farmers_signup.last_name) ASC
                LIMIT :item_per_page OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':item_per_page', $item_per_page, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch farmers whose is_approve is 1 (Approved farmers)
    public function getApprovedFarmers($page = 1, $item_per_page = 10) {
        $page = $page <= 0 ? 1 : $page; 
        $offset = ($page - 1) * $item_per_page;

        // SQL query to fetch approved farmers with associated crops and barangays
        $sql = "SELECT 
                    farmers_signup.*, 
                    crops.crop_name, 
                    barangay.barangay_name 
                FROM 
                    farmers_signup
                LEFT JOIN 
                    crops ON farmers_signup.crop_id = crops.id
                LEFT JOIN 
                    barangay ON farmers_signup.barangay_id = barangay.id
                WHERE 
                    farmers_signup.is_approve = 1
                ORDER BY 
                    CONCAT(farmers_signup.first_name, farmers_signup.last_name) ASC
                LIMIT :item_per_page OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':item_per_page', $item_per_page, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to move approved farmers from farmers_signup to farmers table
    public function moveApprovedFarmersToFarmersTable() {
        // Fetch all approved farmers from the farmers_signup table
        $approvedFarmers = $this->getApprovedFarmers();
    
        foreach ($approvedFarmers as $farmer) {
            // Prepare the SQL to insert into farmers table
            $sql = "INSERT INTO farmers 
                    (password, first_name, middle_name, last_name, extension_name, sex, contact_number, landline_num, area, purok, street, city, province, region, birthdate, birthplace, crop_id, barangay_id, role_id) 
                    VALUES 
                    (:password, :first_name, :middle_name, :last_name, :extension_name, :sex, :contact_number, :landline_num, :area, :purok, :street, :city, :province, :region, :birthdate, :birthplace, :crop_id, :barangay_id, :role_id)";
    
            $stmt = $this->pdo->prepare($sql);

            // Bind all the parameters based on your table structure
            $stmt->bindParam(':password', $farmer['password']); // Ensure the password is hashed properly
            $stmt->bindParam(':first_name', $farmer['first_name']);
            $stmt->bindParam(':middle_name', $farmer['middle_name']);
            $stmt->bindParam(':last_name', $farmer['last_name']);
            $stmt->bindParam(':extension_name', $farmer['extension_name']);
            $stmt->bindParam(':sex', $farmer['sex']);
            $stmt->bindParam(':contact_number', $farmer['contact_number']);
            $stmt->bindParam(':landline_num', $farmer['landline_num']);
            $stmt->bindParam(':area', $farmer['area']);
            $stmt->bindParam(':purok', $farmer['purok']);
            $stmt->bindParam(':street', $farmer['street']);
            $stmt->bindParam(':city', $farmer['city']);
            $stmt->bindParam(':province', $farmer['province']);
            $stmt->bindParam(':region', $farmer['region']);
            $stmt->bindParam(':birthdate', $farmer['birthdate']);
            $stmt->bindParam(':birthplace', $farmer['birthplace']);
            $stmt->bindParam(':crop_id', $farmer['crop_id']);
            $stmt->bindParam(':barangay_id', $farmer['barangay_id']);
            $stmt->bindParam(':role_id', $farmer['role_id']); // Assuming it's always '1' for farmers
    
            // Execute the insertion
            if ($stmt->execute()) {
                // Optionally: After inserting, delete the farmer from the farmers_signup table
                $deleteSql = "DELETE FROM farmers_signup WHERE id = :id";
                $deleteStmt = $this->pdo->prepare($deleteSql);
                $deleteStmt->bindParam(':id', $farmer['id']);
                $deleteStmt->execute();
            }
        }
    }
}
