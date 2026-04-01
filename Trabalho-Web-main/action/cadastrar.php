<?php
require_once "../config/db.php";

$usuario = trim($_POST["usuario"]);
$email   = trim($_POST["email"]);
$senha   = trim($_POST["senha"]);

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

try {
  $stmt = $pdo->prepare("INSERT INTO usuarios (usuario, email, senha) VALUES (?, ?, ?)");
  $stmt->execute([$usuario, $email, $senhaHash]);
  header("Location: login.php?cadastro=ok");
} catch (PDOException $e) {
  header("Location: cadastro.php?erro=usuario_existe");
}
exit;
?>