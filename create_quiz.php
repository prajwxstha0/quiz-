<?php
include 'db.php';
session_start();

// Check if the user is logged in and is an admin (you might need to add admin logic)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    
    // Insert the quiz into the database
    $stmt = $conn->prepare("INSERT INTO quizzes (title) VALUES (?)");
    $stmt->bind_param("s", $title);
    if ($stmt->execute()) {
        $quiz_id = $stmt->insert_id;
        header("Location: add_question.php?quiz_id=$quiz_id"); // Redirect to add questions
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Quiz</title>
    <link rel="stylesheet" href="create_quiz.css">
</head>
<body>
    <div class="container">
        <h1>Create a New Quiz</h1>
        <form method="POST">
            <label for="title">Quiz Title:</label>
            <input type="text" id="title" name="title" required>
            <button type="submit">Create Quiz</button>
        </form>
    </div>
</body>
</html>

