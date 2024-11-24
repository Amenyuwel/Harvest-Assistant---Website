<?php

class PriceHistory extends Models {
    public function __construct($table = 'price_history') {
        parent::__construct($table);
    }

    // Method to fetch all price history records
    public function getAllPriceHistory() {
        $sql = "SELECT ph.id, c.crop_name, ph.old_price, ph.new_price, ph.change_date 
                FROM price_history ph 
                JOIN crop_price cp ON ph.crop_id = cp.crop_id 
                JOIN crops c ON cp.crop_id = c.id  -- Joining with crops table to get the crop name
                ORDER BY ph.change_date DESC";  // Sorting by change date in descending order

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Execute and return results
        } catch (PDOException $e) {
            // You may choose to handle the exception differently based on your application's requirements
            throw new Exception('Error fetching price history: ' . $e->getMessage());
        }
    }
}
