<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quiz_id = $_POST['quiz_id'];
    $user_id = $_SESSION['user_id'];
    $answers = $_POST['answers'];

    // Clear previous answers for this user and quiz if re-taking
    $deletePreviousAnswers = $conn->prepare("DELETE FROM user_answers WHERE user_id = ? AND quiz_id = ?");
    $deletePreviousAnswers->bind_param("ii", $user_id, $quiz_id);
    $deletePreviousAnswers->execute();

    // Insert each answer into user_answers
    foreach ($answers as $question_id => $answer_option) {
        $stmt = $conn->prepare("INSERT INTO user_answers (user_id, quiz_id, question_id, answer_option) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiii", $user_id, $quiz_id, $question_id, $answer_option);
        $stmt->execute();
    }

    // Redirect to score page
    header("Location: score.php?quiz_id=$quiz_id");
    exit;
}
?>


