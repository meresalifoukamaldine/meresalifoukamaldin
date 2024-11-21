<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site des événements du Bénin</title>
    <link rel="stylesheet" href="assets/css/style4.css">
</head>
<body style="background-image: url('assets/images/back5.jpg'); opacity: 30px;background-size: cover; background-position: center; background-attachment: fixed;" >

<!-- Menu de navigation -->
<nav>
    <ul>
        <li><a href="logout.php">Deconnexion</a></li>
    </ul>
</nav>

<!-- Titre principal -->
<header>
    <h1>Tableau de bord Utilisateur</h1>
    <h2>Liste des événements à venir</h2>
</header>
    <P>
    <a href="my_events.php">Voir les événements auxquels je participe</a>
    </P>
<!-- Section des événements -->
<section class="events-container">

<?php
session_start();
if ($_SESSION['role'] != 'user') {
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

// Récupération des événements depuis la base de données
$query = "SELECT * FROM events";
$events = $conn->query($query);

while ($event = $events->fetch_assoc()) {
    echo "<div class='event-card' style='background-image: url(\"/uploads/{$event['image']}\");'>";
    
    // Informations sur l'événement avec fond semi-transparent
    echo "<div class='event-info'>
            <h3>{$event['name']}</h3>
            <p><strong>Date de début :</strong> {$event['start_date']}</p>
            <p><strong>Date de fin :</strong> {$event['end_date']}</p>
            <p><strong>Description :</strong> {$event['description']}</p>
          </div>";
    
    // Boutons d'interaction
    echo "<div class='event-actions'>
            <a href='participate.php?event_id={$event['id']}' class='btn-participate'>Participer</a>
            <a href='like_events.php?event_id={$event['id']}' class='btn-like'>J'aime</a>
          </div>";
    
    // Formulaire pour ajouter un avis
    echo "<form action='add_feedback.php' method='POST' class='feedback-form'>
            <input type='hidden' name='event_id' value='{$event['id']}'>
            <textarea name='feedback' placeholder='Votre avis...'></textarea>
            <input type='submit' value='Ajouter un avis'>
          </form>";
    
    echo "</div>"; // Fermeture de la div .event-card
}
?>

</section> <!-- Fin de .events-container -->

</body>
</html>
