<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site des événements du Bénin</title>
    <link rel="stylesheet" href="assets/css/style2.css">
</head>
<body style="background-image: url('assets/images/bg2.jpg'); background-size: cover; background-position: center; background-attachment: fixed;">

   

<?php
session_start();
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');  // À adapter selon votre configuration
define('DB_PASSWORD', '');
define('DB_DATABASE', 'evenement_benin');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connexion à la base de données échouée: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        if ($user['role'] == 'admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: user_dashboard.php');
        }
    } else {
        echo "Identifiants incorrects!";
        header('Location: login.php');
    }
}
?>

<form method="post">
    <h3>Connexion</h3></br></br>
    Email: <input type="email" name="email" required><br>
    Mot de passe: <input type="password" name="password" required><br>
    <input type="submit" value="Se connecter"></br></br>

    <p class="signup-link">Pas encore inscrit ? <a href="register.php">Inscrivez-vous ici</a></p>
</form>
</body>
</html>
