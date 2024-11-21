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

// Vérifier si l'utilisateur participe déjà
$query = "SELECT * FROM participations WHERE user_id = ? AND event_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $event_id);
$stmt->execute();
$result = $stmt->get_result();

$already_participating = $result->num_rows > 0;

if (!$already_participating) {
    // Insérer la participation
    $query = "INSERT INTO participations (user_id, event_id, is_participating) VALUES (?, ?, 1)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $event_id);
    $participation_success = $stmt->execute();
} 

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Participation</title>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            <?php if ($already_participating): ?>
                alert("Vous participez déjà à cet événement.");
            <?php elseif (isset($participation_success) && $participation_success): ?>
                alert("Participation confirmée! Vous êtes maintenant inscrit à cet événement.");
            <?php else: ?>
                alert("Une erreur est survenue. Veuillez réessayer.");
            <?php endif; ?>

            // Redirection après la notification
            setTimeout(function() {
                window.location.href = "my_events.php";
            }, 100); // Délai de 1 seconde
        });
    </script>
</head>
<body>
</body>
</html>
