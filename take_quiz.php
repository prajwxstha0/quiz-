<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['quiz_id'])) {
    die("No quiz selected.");
}

$quiz_id = $_GET['quiz_id'];

// Fetch quiz questions
$query = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$query->bind_param("i", $quiz_id);
$query->execute();
$questions = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Take Quiz</title>
    <link rel="stylesheet" href="take_quiz.css">
</head>
<body>
    <!-- Timer Container -->
    <div class="header">
        <div id="timer">Time Left: 1:00</div>
    </div>

    <!-- Quiz Form -->
    <form action="submit_score.php" method="POST">
        <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
        
        <?php while ($question = $questions->fetch_assoc()): ?>
            <div class="question-block">
                <p class="question-text"><?php echo htmlspecialchars($question['question_text']); ?></p>

                <?php
                $question_id = $question['id'];
                $options_query = $conn->prepare("SELECT * FROM options WHERE question_id = ?");
                $options_query->bind_param("i", $question_id);
                $options_query->execute();
                $options = $options_query->get_result();
                ?>

                <?php while ($option = $options->fetch_assoc()): ?>
                    <label class="option-label">
                        <input type="radio" name="answers[<?php echo $question_id; ?>]" value="<?php echo $option['option_number']; ?>" onclick="highlightSelected(<?php echo $question_id; ?>)" required>
                        <?php echo htmlspecialchars($option['option_text']); ?>
                    </label><br>
                <?php endwhile; ?>
            </div>
            <br>
        <?php endwhile; ?>
        
        <button type="submit" class="submit-button">Submit Quiz</button>
    </form>

    <!-- Timer JavaScript -->
    <script>
        let timeLeft = 60; // Set timer in seconds

        function startTimer() {
            const timerDisplay = document.getElementById("timer");
            
            const interval = setInterval(() => {
                if (timeLeft <= 0) {
                    clearInterval(interval);
                    alert("Time's up! Submitting your quiz.");
                    document.querySelector("form").submit();
                } else {
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    timerDisplay.textContent = `Time Left: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                    timeLeft--;
                }
            }, 1000);
        }

        document.addEventListener("DOMContentLoaded", startTimer);

        function highlightSelected(questionId) {
            const options = document.getElementsByName(`answers[${questionId}]`);
            options.forEach(option => {
                const label = option.parentElement;
                label.style.backgroundColor = option.checked ? "lightgreen" : "";
            });
        }
    </script>
</body>
</html>


