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
$event_id = $_GET['event_id'];  // ID de l'événement à partir de l'URL
$feedback = $_POST['feedback'];  // Le feedback de l'utilisateur

// Vérification si l'utilisateur participe déjà à l'événement
$query = "SELECT * FROM participations WHERE user_id = ? AND event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Si l'utilisateur participe déjà, mettre à jour le feedback
    $query = "UPDATE participations SET feedback = ? WHERE user_id = ? AND event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $feedback, $user_id, $event_id);
} else {
    // Si l'utilisateur ne participe pas encore, insérer un nouvel enregistrement
    $query = "INSERT INTO participations (user_id, event_id, is_participating, feedback) VALUES (?, ?, 1, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $user_id, $event_id, $feedback);
}

// Exécution de la requête et retour au dashboard avec un message de confirmation
if ($stmt->execute()) {
    echo "<script>alert('Votre feedback a été ajouté !'); window.location.href = 'user_dashboard.php';</script>";
} else {
    echo "<script>alert('Une erreur est survenue lors de l\'ajout du feedback.'); window.location.href = 'user_dashboard.php';</script>";
}

$stmt->close();
$conn->close();
?>
