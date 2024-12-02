<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['quiz_id'])) {
    die("Quiz ID is missing.");
}

$quiz_id = $_GET['quiz_id'];

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_text = $_POST['question_text'];
    $correct_option = $_POST['correct_option'];
    $options = $_POST['options'];

    // Insert question into the database
    $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text, correct_option) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $quiz_id, $question_text, $correct_option);
    
    if ($stmt->execute()) {
        $question_id = $stmt->insert_id;

        // Insert each option into the database
        foreach ($options as $index => $option_text) {
            $option_number = $index + 1;  // Option numbers start at 1
            $stmt_option = $conn->prepare("INSERT INTO options (question_id, option_number, option_text) VALUES (?, ?, ?)");
            $stmt_option->bind_param("iis", $question_id, $option_number, $option_text);
            $stmt_option->execute();
        }

        // Redirect to refresh the form for adding the next question
        header("Location: add_question.php?quiz_id=$quiz_id&success=1");
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
    <title>Add Questions</title>
    <link rel="stylesheet" href="add_question.css">
    
</head>
<body>
<div class="container">
        <h1>Add Questions to Quiz</h1>
        <p><a href="quizzes.php">Go Back to Quizzes</a></p>
        <?php if (isset($_GET['success'])): ?>
            <p class="success-message">Question added successfully!</p>
        <?php endif; ?>

        <form method="POST">
            <label for="question_text">Question:</label>
            <textarea id="question_text" name="question_text" required></textarea><br>

            <div id="options-container">
                <div class="option-group">
                    <label>Option 1:</label>
                    <input type="text" name="options[]" required>
                </div>
                <div class="option-group">
                    <label>Option 2:</label>
                    <input type="text" name="options[]" required>
                </div>
                <div class="option-group">
                    <label>Option 3:</label>
                    <input type="text" name="options[]" required>
                </div>
                <div class="option-group">
                    <label>Option 4:</label>
                    <input type="text" name="options[]" required>
                </div>
            </div>

            <label>Correct Option (number):</label>
            <input type="number" name="correct_option" min="1" max="4" required><br>

            <button type="button" onclick="addOption()">Add Another Option</button><br><br>
            <button type="submit">Add Question</button>
        </form>
    </div>

    <script>
   let optionCount = 4;

function addOption() {
    optionCount++;
    const container = document.getElementById('options-container');
    const newOption = document.createElement('div');
    newOption.classList.add('option-group');
    newOption.innerHTML = `
        <label>Option ${optionCount}:</label>
        <input type="text" name="options[]" required>
    `;
    container.appendChild(newOption);
}

    </script>
</body>
</html>
