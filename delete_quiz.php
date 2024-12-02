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

// Start a transaction to ensure data integrity
$conn->begin_transaction();

try {
    // Step 1: Delete answers associated with this quiz's questions from user_answers
    $deleteUserAnswersQuery = $conn->prepare("
        DELETE user_answers FROM user_answers
        JOIN questions ON user_answers.question_id = questions.id
        WHERE questions.quiz_id = ?
    ");
    $deleteUserAnswersQuery->bind_param("i", $quiz_id);
    $deleteUserAnswersQuery->execute();

    // Step 2: Delete options associated with each question in this quiz
    $deleteOptionsQuery = $conn->prepare("
        DELETE options FROM options
        JOIN questions ON options.question_id = questions.id
        WHERE questions.quiz_id = ?
    ");
    $deleteOptionsQuery->bind_param("i", $quiz_id);
    $deleteOptionsQuery->execute();

    // Step 3: Delete questions associated with the quiz
    $deleteQuestionsQuery = $conn->prepare("DELETE FROM questions WHERE quiz_id = ?");
    $deleteQuestionsQuery->bind_param("i", $quiz_id);
    $deleteQuestionsQuery->execute();

    // Step 4: Delete the quiz itself
    $deleteQuizQuery = $conn->prepare("DELETE FROM quizzes WHERE id = ?");
    $deleteQuizQuery->bind_param("i", $quiz_id);
    $deleteQuizQuery->execute();

    // Commit the transaction
    $conn->commit();

    // Redirect to quizzes.php with success message
    header("Location: quizzes.php?deleted=1");
    exit;

} catch (Exception $e) {
    // Roll back transaction if there was an error
    $conn->rollback();
    echo "Error deleting quiz: " . $e->getMessage();
}
?>
