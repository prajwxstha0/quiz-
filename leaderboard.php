<?php
include 'db.php';

$quiz_id = $_GET['quiz_id'];

// Fetch scores
$query = $conn->prepare("
    SELECT users.username, scores.score 
    FROM scores 
    JOIN users ON scores.user_id = users.id 
    WHERE scores.quiz_id = ? 
    ORDER BY scores.score DESC 
    LIMIT 10
");
$query->bind_param("i", $quiz_id);
$query->execute();
$scores = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leaderboard</title>
</head>

<body>
    <h1>Leaderboard</h1>
    <table>
        <tr><th>User</th><th>Score</th></tr>
        <?php while ($row = $scores->fetch_assoc()): ?>
            <tr><td><?php echo htmlspecialchars($row['username']); ?></td><td><?php echo $row['score']; ?></td></tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
