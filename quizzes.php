<?php

include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all quizzes
$query = "SELECT * FROM quizzes";
$result = $conn->query($query);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Quizzes</title>
    <link rel="stylesheet" href="quizzes.css">
</head>
<body>
    <div class="container">
        <h1>All Quizzes</h1>
        <a href="create_quiz.php" class="create-quiz">Create a New Quiz</a>

        <ul class="quiz-list">
            <?php while ($quiz = $result->fetch_assoc()): ?>
                <li>
                    <span><?php echo htmlspecialchars($quiz['title']); ?></span>
                    <div>
                        <a href="take_quiz.php?quiz_id=<?php echo $quiz['id']; ?>" class="take-quiz">Take Quiz</a>
                        <a href="delete_quiz.php?quiz_id=<?php echo $quiz['id']; ?>" class="delete-quiz" onclick="return confirm('Are you sure you want to delete this quiz?');">Delete</a>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>

        <?php if (isset($_GET['deleted'])): ?>
            <p class="success-message">Quiz deleted successfully!</p>
        <?php endif; ?>
    </div>
</body>
</html>

