<?php
// includes/auth_check.php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION["usuarioLogado"])) {
    // Se não estiver logado, redireciona para o login
    header("Location: ../action/login.php");
    exit;
}

// Opcional: pegar dados do usuário para usar nas páginas
$usuarioLogado = $_SESSION["usuarioLogado"];
?>