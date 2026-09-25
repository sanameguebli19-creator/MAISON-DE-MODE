<?php
// ============================================================
// CONNEXION UTILISATEUR — Maison de Mode
// ============================================================
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../connexion.php');
    exit();
}

if (empty($_POST['email']) || empty($_POST['password'])) {
    header('Location: ../connexion.php?error=champs_vides');
    exit();
}

$email    = trim($_POST['email']);
$password = $_POST['password'];

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['mot_de_passe'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nom']     = $user['nom'];
    $_SESSION['email']   = $user['email'];
    session_regenerate_id(true);
    header('Location: ../dashboard.php');
    exit();
} else {
    header('Location: ../connexion.php?error=1');
    exit();
}
?>