<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Prepare and execute the query
    $query = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $query->bind_param("ss", $username, $password);

    if ($query->execute()) {
        echo "Registration successful! Redirecting to login page...";
        header("Location: login.html");
        exit;
    } else {
        echo "Error: " . $query->error; // Display specific error message
    }
}
?>
