<?php

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "Lab_5b";

$message = ""; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $matric = htmlspecialchars($_POST['matric']);
    $name = htmlspecialchars($_POST['name']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Secure password hashing
    $role = htmlspecialchars($_POST['role']);

    try {

        $conn = new mysqli($servername, $db_username, $db_password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // Prepare and bind SQL statement
        $stmt = $conn->prepare("INSERT INTO users (matric, name, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $matric, $name, $password, $role);

        // Execute the query
        if ($stmt->execute()) {
            $message = "<h3 style='color: green;'>Registration successful!</h3>";
        } else {
            throw new Exception("Error: " . $stmt->error);
        }

        // Close connections
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        $message = "<h3 style='color: red;'>Error: " . $e->getMessage() . "</h3>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        /* General Body Styles */
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #6E85B7, #BFEAF5);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
        }

        /* Card Container */
        .container {
            background: #fff;
            width: 100%;
            max-width: 400px;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1.2s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Form Header */
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 1.8em;
            color: #4A6FA5;
        }

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 0.9em;
            color: #4A4A4A;
        }

        input, select {
            padding: 12px 15px;
            font-size: 1em;
            border: 1px solid #ddd;
            border-radius: 10px;
            outline: none;
            transition: all 0.3s;
        }

        input:focus, select:focus {
            border-color: #4A6FA5;
            box-shadow: 0 0 10px rgba(74, 111, 165, 0.2);
        }

        button {
            padding: 12px 15px;
            font-size: 1em;
            border: none;
            background: linear-gradient(135deg, #4A6FA5, #BFEAF5);
            color: white;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        button:hover {
            background: linear-gradient(135deg, #BFEAF5, #4A6FA5);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .message {
            text-align: center;
            margin-top: 15px;
            font-size: 0.9em;
        }

        @media (max-width: 768px) {
            .container {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php if ($message) echo "<div class='message'>$message</div>"; ?>
        <form method="POST" action="">
            <label for="matric">Matric</label>
            <input type="text" id="matric" name="matric" placeholder="Enter Matric Number" required>

            <label for="name">Name</label>
            <input type="text" id="name" name="name" placeholder="Enter Full Name" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter Password" required>

            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="">Please select</option>
                <option value="Student">Student</option>
                <option value="Lecturer">Lecturer</option>
            </select>

            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
