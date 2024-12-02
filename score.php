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
$user_id = $_SESSION['user_id'];

// Fetch questions and user’s answers
$questionsQuery = $conn->prepare("
    SELECT q.id AS question_id, q.question_text, q.correct_option, u.answer_option 
    FROM questions q
    LEFT JOIN user_answers u ON q.id = u.question_id AND u.user_id = ? 
    WHERE q.quiz_id = ?
");
$questionsQuery->bind_param("ii", $user_id, $quiz_id);
$questionsQuery->execute();
$questionsResult = $questionsQuery->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz Results</title>
    <link rel="stylesheet" href="score.css">
</head>
<body>
    <div class="results-container">
        <h1>Quiz Results</h1>

        <?php
        $score = 0;
        $totalQuestions = 0;

        while ($question = $questionsResult->fetch_assoc()) {
            $totalQuestions++;
            $isCorrect = ($question['answer_option'] == $question['correct_option']);
            if ($isCorrect) $score++;

            echo "<div class='question-block'>";
            echo "<p><strong>Question:</strong> " . htmlspecialchars($question['question_text']) . "</p>";
            echo "<p><strong>Your Answer:</strong> " . htmlspecialchars(getOptionText($conn, $question['question_id'], $question['answer_option'])) . "</p>";
            
            if ($isCorrect) {
                echo "<p class='correct'><strong>Correct!</strong></p>";
            } else {
                echo "<p class='incorrect'><strong>Incorrect!</strong></p>";
                echo "<p><strong>Correct Answer:</strong> " . htmlspecialchars(getOptionText($conn, $question['question_id'], $question['correct_option'])) . "</p>";
            }
            echo "</div><br>";
        }

        echo "<h2>Your Score: $score / $totalQuestions</h2>";

        function getOptionText($conn, $question_id, $option_number) {
            $optionQuery = $conn->prepare("SELECT option_text FROM options WHERE question_id = ? AND option_number = ?");
            $optionQuery->bind_param("ii", $question_id, $option_number);
            $optionQuery->execute();
            $optionResult = $optionQuery->get_result();
            $option = $optionResult->fetch_assoc();
            return $option ? $option['option_text'] : "No answer selected";
        }
        ?>

        <a href="quizzes.php">
            <input type="button" value="BACK TO QUIZZES" class="back-button">
        </a>
    </div>

    <script>
        // Add hover effect to the back button
        document.querySelector('.back-button').addEventListener('mouseover', function() {
            this.style.backgroundColor = '#5A9BFF';
            this.style.cursor = 'pointer';
        });

        document.querySelector('.back-button').addEventListener('mouseout', function() {
            this.style.backgroundColor = '#007bff';
        });
    </script>
</body>
</html>


