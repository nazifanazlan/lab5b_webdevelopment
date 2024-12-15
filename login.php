<?php
session_start(); // Start the session

// Database configuration
$servername = "localhost";
$db_username = "root";   
$db_password = "";          
$dbname = "Lab_5b";

// Handle login
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matric = htmlspecialchars($_POST['matric']);
    $password = $_POST['password'];

    try {
        $conn = new mysqli($servername, $db_username, $db_password, $dbname);
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // Prepare and execute query
        $stmt = $conn->prepare("SELECT matric, name, password FROM users WHERE matric = ?");
        $stmt->bind_param("s", $matric);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Successful login
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $user['name'];
                $_SESSION['matric'] = $user['matric'];
                header("Location: display.php"); 
                exit;
            } else {
                $message = "<h3 style='color: red;'>Invalid password!</h3>";
            }
        } else {
            $message = "<h3 style='color: red;'>Matric not found!</h3>";
        }

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
    <title>Login</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #6E85B7, #BFEAF5);
            color: #333;
        }
        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 350px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #4A6FA5;
        }
        input {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4A6FA5;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #3a5a85;
        }
        .register-link {
            margin-top: 15px;
            display: block;
            color: #4A6FA5;
            text-decoration: none;
        }
        .register-link:hover {
            text-decoration: underline;
        }
        .message {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($message) echo "<div class='message'>$message</div>"; ?>
        <form method="POST" action="">
            <input type="text" name="matric" placeholder="Enter Matric Number" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit">Login</button>
        </form>
        <a href="register.php" class="register-link">Don't have an account? Register here</a>
    </div>
</body>
</html>
