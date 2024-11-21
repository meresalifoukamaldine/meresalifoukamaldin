<?php
session_start();

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'evenement_benin');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connexion à la base de données échouée: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$event_id = $_GET['event_id'];

// Check if the user is already participating in the event
$query = "SELECT * FROM participations WHERE user_id = ? AND event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // If the user already participates, update the 'liked' field
    $query = "UPDATE participations SET liked = 1 WHERE user_id = ? AND event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $event_id);
} else {
    // If not, insert a new participation entry with 'liked' set to 1
    $query = "INSERT INTO participations (user_id, event_id, is_participating, liked) VALUES (?, ?, 1, 1)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $event_id);
}

// Execute the query and provide feedback to the user
if ($stmt->execute()) {
    echo "<script>alert('Vous avez aimé cet événement !'); window.location.href = 'user_dashboard.php';</script>";
} else {
    echo "<script>alert('Une erreur est survenue lors de l\'enregistrement de votre like.'); window.location.href = 'user_dashboard.php';</script>";
}

$stmt->close();
$conn->close();
?>
