<?php
session_start();
require_once "../config/db.php";

$usuario = trim($_POST["usuario"]);
$senha   = trim($_POST["senha"]);

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
$stmt->execute([$usuario]);
$user = $stmt->fetch();

if ($user && password_verify($senha, $user["senha"])) {
  $_SESSION["usuarioLogado"] = $user["usuario"];
  header("Location: ../index.php");
} else {
  header("Location: login.php?erro=invalido");
}
exit;
?>