<?php
// Include your database connection script
include 'conn.php';

// Query to fetch all FAQs from the database
$sql = "SELECT question, answer FROM faq";
$result = $conn->query($sql);

// Initialize an empty array to hold the fetched data
$faqList = array();

// Check if there are results, and fetch each row into the array
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $faqList[] = $row;
    }
}

// Return the data as a JSON response
header('Content-Type: application/json');
echo json_encode($faqList);

// Close the database connection
$conn->close();
?>
