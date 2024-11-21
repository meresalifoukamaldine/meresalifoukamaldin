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

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    // Gestion du téléchargement de l'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/"; // Dossier de destination pour les images
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . uniqid() . "_" . $image_name; // Nom de fichier unique pour éviter les conflits
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Vérifier le type de fichier
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (in_array($imageFileType, $allowed_types)) {
            // Déplacer le fichier téléchargé vers le dossier cible
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Préparer la requête d'insertion
                $query = "INSERT INTO events (name, description, image, start_date, end_date) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssss", $name, $description, $target_file, $start_date, $end_date);
                
                if ($stmt->execute()) {
                    echo "<div class='alert success'>L'événement a été ajouté avec succès !</div>";
                    header('Location: admin_dashboard.php');
                } else {
                    echo "<div class='alert error'>Erreur lors de l'ajout de l'événement : " . $conn->error . "</div>";
                }

                $stmt->close();
            } else {
                echo "<div class='alert error'>Erreur lors du téléchargement de l'image.</div>";
            }
        } else {
            echo "<div class='alert error'>Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.</div>";
        }
    } else {
        echo "<div class='alert error'>Veuillez sélectionner une image à télécharger.</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publier un Événement</title>
    <link rel="stylesheet" href="assets/css/style7.css">
    
</head>
<body>

<header>
    <h1>Publier un Nouvel Événement</h1>
    <div style="text-align: right;">
        <a href="admin_dashboard.php" class="back-button">← Retour au tableau de bord</a>
    </div>
</header>

<div class="container">
    <form action="add_event.php" method="POST" enctype="multipart/form-data">
        <label for="name">Nom de l'événement :</label>
        <input type="text" name="name" id="name" required>

        <label for="description">Description :</label>
        <textarea name="description" id="description" rows="4" required></textarea>

        <label for="image">Image :</label>
        <input type="file" name="image" id="image" accept="image/*" required>

        <label for="start_date">Date de début :</label>
        <input type="datetime-local" name="start_date" id="start_date" required>

        <label for="end_date">Date de fin :</label>
        <input type="datetime-local" name="end_date" id="end_date" required>

        <button type="submit">Publier l'événement</button>
    </form>
</div>

<footer>
    <p>&copy; 2024 Site des événements du Bénin | Tous droits réservés</p>
</footer>

</body>
</html>
