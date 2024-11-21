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

$query = "
    SELECT e.id AS event_id, e.name AS event_name, e.description, e.image, e.start_date, e.end_date
    FROM events e
    INNER JOIN participations p ON e.id = p.event_id
    WHERE p.user_id = ? AND p.is_participating = 1
    ORDER BY e.start_date DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes événements</title>
    <link rel="stylesheet" href="assets/css/style5.css">
</head>
<body style="background-image: url('assets/images/back2.jpg'); background-size: cover; background-position: center; background-attachment: fixed;">

<div class="container">
     <!-- Bouton de retour -->
     <div class="back-button">
        <a href="user_dashboard.php" class="button">Retour</a>
    </div>
    <h1>Événements auxquels je participe</h1>

    <?php if (count($events) > 0): ?>
        <div class="events-list">
            <?php foreach ($events as $event): ?>
                <div class="event-item">
                    <div class="event-image">
                        <?php if (!empty($event['image'])): ?>
                            <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="Image de l'événement">
                        <?php else: ?>
                            <img src="assets/images/default-event.jpg" alt="Image par défaut">
                        <?php endif; ?>
                    </div>
                    <div class="event-details">
                        <h2><?php echo htmlspecialchars($event['event_name']); ?></h2>
                        <p><strong>Description :</strong> <?php echo htmlspecialchars($event['description']); ?></p>
                        <p><strong>Date de début :</strong> <?php echo htmlspecialchars($event['start_date']); ?></p>
                        <p><strong>Date de fin :</strong> <?php echo htmlspecialchars($event['end_date']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="no-events">Vous ne participez à aucun événement pour le moment.</p>
    <?php endif; ?>
</div>

</body>
</html>
