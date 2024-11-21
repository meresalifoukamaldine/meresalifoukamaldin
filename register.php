<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site des événements du Bénin</title>
    <link rel="stylesheet" href="assets/css/style3.css">
</head>
<body style="background-image: url('assets/images/bg2.jpg'); background-size: cover; background-position: center; background-attachment: fixed;">

<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');  // À adapter selon votre configuration
define('DB_PASSWORD', '');
define('DB_DATABASE', 'evenement_benin');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connexion à la base de données échouée: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $query = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
    if ($conn->query($query) === TRUE) {
        echo "Inscription réussie!";
        header('Location: login.php');
    } else {
        echo "Erreur : " . $conn->error;
    }
}
?>

<form method="post">
    Nom: <input type="text" name="name" required><br>
    Email: <input type="email" name="email" required><br>
    Mot de passe: <input type="password" name="password" required><br>
    Rôle: 
    <select name="role">
        <option value="user">Utilisateur</option>
        <option value="admin">Administrateur</option>
    </select><br>
    <input type="submit" value="S'inscrire">
</form>
</body>
</html>
