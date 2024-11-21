// /config/config.php
<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');  // À adapter selon votre configuration
define('DB_PASSWORD', '');
define('DB_DATABASE', 'evenement_benin');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connexion à la base de données échouée: " . $conn->connect_error);
}
?>
