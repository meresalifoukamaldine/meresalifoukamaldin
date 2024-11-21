<?php
session_start();

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'evenement_benin');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connexion à la base de données échouée : " . $conn->connect_error);
}

// Vérifiez que l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

// Récupération de tous les événements et des participants
$query = "
    SELECT e.id AS event_id, e.name AS event_name, e.description, e.image, e.start_date, e.end_date,
           u.name AS user_name, u.email AS user_email, p.is_participating, p.feedback, p.liked
    FROM events e
    LEFT JOIN participations p ON e.id = p.event_id AND p.is_participating = 1
    LEFT JOIN users u ON p.user_id = u.id
    ORDER BY e.start_date DESC, u.name ASC;
";
$result = $conn->query($query);

$events = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $event_id = $row['event_id'];
        if (!isset($events[$event_id])) {
            $events[$event_id] = [
                'name' => $row['event_name'],
                'description' => $row['description'],
                'image' => $row['image'],
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date'],
                'participants' => []
            ];
        }
        if ($row['user_name']) {
            $events[$event_id]['participants'][] = [
                'name' => $row['user_name'],
                'email' => $row['user_email'],
                'feedback' => $row['feedback'],
                'liked' => $row['liked']
            ];
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des participants aux événements</title>
    <link rel="stylesheet" href="assets/css/style8.css">
</head>
<body   style="background-image: url('assets/images/bg1.png'); background-size: cover; background-position: center; background-attachment: fixed;">

<div class="container">
    <h1>Liste des participants aux événements</h1>
    <div class="back-button">
        <a href="admin_dashboard.php" class="btn-retour">← Retour au Tableau de Bord</a>
    </div>

    <?php foreach ($events as $event): ?>
        <div class="event">
            <h2><?php echo htmlspecialchars($event['name']); ?></h2>
            
            <?php if (!empty($event['image'])): ?>
                <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="Image de l'événement" class="event-image">
            <?php endif; ?>
            
            <p><strong>Description :</strong> <?php echo htmlspecialchars($event['description']); ?></p>
            
            <h3>Participants :</h3>
            <?php if (count($event['participants']) > 0): ?>
                <ul class="participant-list">
                    <?php foreach ($event['participants'] as $participant): ?>
                        <li>
                            <strong>Nom :</strong> <?php echo htmlspecialchars($participant['name']); ?> |
                            <strong>Email :</strong> <?php echo htmlspecialchars($participant['email']); ?> |
                            <strong>Feedback :</strong> <?php echo htmlspecialchars($participant['feedback']); ?> |
                            <strong>A aimé :</strong> <?php echo $participant['liked'] ? 'Oui' : 'Non'; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="no-participants">Aucun participant pour cet événement.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
