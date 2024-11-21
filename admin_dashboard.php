<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    die("Accès interdit");
}

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'evenement_benin');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connexion à la base de données échouée: " . $conn->connect_error);
}

$query = "SELECT * FROM events";
$events = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Administrateur</title>
    <link rel="stylesheet" href="assets/css/style6.css">
    
</head>
<body>

<header>
    <h1>Tableau de bord Administrateur</h1>
</header>
</br></br>
<nav>
    
        <a href="logout.php">Deconnexion</a>

</nav>
<div class="container">
    <div class="dashboard-links">
        <a href="add_event.php">Publier un événement</a>
        <a href="admin_participants.php">Liste des Participants</a>
    </div>

    <h2>Liste des événements</h2>

    <?php if ($events->num_rows > 0): ?>
        <ul class="event-list">
            <?php while ($event = $events->fetch_assoc()): ?>
                <li class="event-item">
                    <div class="event-image">
                        <?php if (!empty($event['image'])): ?>
                            <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="Image de l'événement">
                        <?php endif; ?>
                    </div>

                    <div class="event-details">
                        <h3><?php echo htmlspecialchars($event['name']); ?></h3>
                        <span><strong>Date de début :</strong> <?php echo htmlspecialchars($event['start_date']); ?></span><br>
                        <span><strong>Date de fin :</strong> <?php echo htmlspecialchars($event['end_date']); ?></span>

                        <p class="description"><strong>Description :</strong> <?php echo htmlspecialchars($event['description']); ?></p>
                    </div>

                    <div class="edit-btn-container">
                        <a href="edit_event.php?event_id=<?php echo $event['id']; ?>" class="edit-btn">Modifier</a>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>Aucun événement trouvé.</p>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2024 Site des événements du Bénin | Tous droits réservés</p>
</footer>

</body>
</html>

<?php
$conn->close();
?>
