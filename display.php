<?php

session_start(); 

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Database configuration
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "Lab_5b";

$usersData = [];
$message = "";

// Handle logout action
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

try {
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Handle form submissions
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['update'])) {
            // Update user details
            $matric = htmlspecialchars($_POST['matric']);
            $name = htmlspecialchars($_POST['name']);
            $role = htmlspecialchars($_POST['role']);

            $stmt = $conn->prepare("UPDATE users SET name = ?, role = ? WHERE matric = ?");
            $stmt->bind_param("sss", $name, $role, $matric);

            if ($stmt->execute()) {
                $message = "<h3 style='color: green;'>User updated successfully!</h3>";
            } else {
                $message = "<h3 style='color: red;'>Error updating user: " . $stmt->error . "</h3>";
            }
            $stmt->close();
        } elseif (isset($_POST['delete'])) {
            // Delete user
            $matric = htmlspecialchars($_POST['matric']);

            $stmt = $conn->prepare("DELETE FROM users WHERE matric = ?");
            $stmt->bind_param("s", $matric);

            if ($stmt->execute()) {
                $message = "<h3 style='color: green;'>User deleted successfully!</h3>";
            } else {
                $message = "<h3 style='color: red;'>Error deleting user: " . $stmt->error . "</h3>";
            }
            $stmt->close();
        }
    }

    // Fetch all users from the database
    $result = $conn->query("SELECT matric, name, role FROM users");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $usersData[] = $row;
        }
    }
    $conn->close();
} catch (Exception $e) {
    die("<h3 style='color: red;'>Error: " . $e->getMessage() . "</h3>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Access List</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #6E85B7, #BFEAF5);
            padding: 20px;
            color: #333;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4A6FA5;
            color: #fff;
        }
        td {
            background-color: #f9f9f9;
        }
        form {
            display: inline-block;
        }
        button {
            background: #4A6FA5;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #3a5a85;
        }
        .message {
            text-align: center;
            margin: 20px 0;
        }
        .logout-btn {
            text-align: center;
            margin-bottom: 20px;
        }
        .logout-btn a {
            text-decoration: none;
            color: white;
            padding: 10px 20px;
            background: #4A6FA5;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .logout-btn a:hover {
            background: #3a5a85;
        }
    </style>
</head>
<body>
    <div class="logout-btn">
        <a href="?logout=true">Log Out</a>
    </div>
    <h2>User Access List</h2>
    <?php if ($message) echo "<div class='message'>$message</div>"; ?>
    <table>
        <thead>
            <tr>
                <th>Matric</th>
                <th>Name</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($usersData)) { ?>
                <?php foreach ($usersData as $user) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['matric']); ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($user['role'])); ?></td>
                        <td>
                            <!-- Update Form -->
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="matric" value="<?php echo htmlspecialchars($user['matric']); ?>">
                                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                <select name="role" required>
                                    <option value="Student" <?php if ($user['role'] === 'Student') echo 'selected'; ?>>Student</option>
                                    <option value="Lecturer" <?php if ($user['role'] === 'Lecturer') echo 'selected'; ?>>Lecturer</option>
                                </select>
                                <button type="submit" name="update">Update</button>
                            </form>
                            <!-- Delete Form -->
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="matric" value="<?php echo htmlspecialchars($user['matric']); ?>">
                                <button type="submit" name="delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="4" style="text-align: center; color: red;">No users found!</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
