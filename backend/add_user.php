<?php
// ============================================================
// INSCRIPTION UTILISATEUR — Maison de Mode
// ============================================================
require_once __DIR__ . "/db.php";

if (empty($_POST['nom']) || empty($_POST['email']) || empty($_POST['password'])) {
    header('Location: ../inscription.php?erreur=champs_vides');
    exit();
}

$nom      = trim($_POST['nom']);
$email    = trim($_POST['email']);
$tel      = trim($_POST['telephone'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../inscription.php?erreur=email_invalide');
    exit();
}

// Vérifier email existant
$stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->rowCount() > 0) {
    header('Location: ../inscription.php?erreur=email_existe');
    exit();
}

// Hasher le mot de passe
$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, telephone) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nom, $email, $hash, $tel]);
    header('Location: ../inscription.php?succes=1');
    exit();
} catch (PDOException $e) {
    header('Location: ../inscription.php?erreur=serveur');
    exit();
}
?>