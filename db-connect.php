<?php
// Database configuration
$servername = "localhost";
$username = "root";      // Change if you use a custom username
$password = "";          // Change if you use a database password
$dbname = "Lab_5b";

// Check if the form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data and sanitize inputs
    $matric = htmlspecialchars($_POST['matric']);
    $name = htmlspecialchars($_POST['name']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
    $role = htmlspecialchars($_POST['role']);

    try {
        // Create a database connection using MySQLi
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check the connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // Prepare an SQL statement
        $stmt = $conn->prepare("INSERT INTO users (matric, name, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $matric, $name, $password, $role);

        // Execute the query
        if ($stmt->execute()) {
            echo "Registration successful!";
        } else {
            throw new Exception("Error: " . $stmt->error);
        }

        // Close connections
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request!";
}
?>
