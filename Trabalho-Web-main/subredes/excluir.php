<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM subredes WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['mensagem'] = "Sub-rede excluída com sucesso!";
    } catch (PDOException $e) {
        $_SESSION['erro'] = "Erro ao excluir sub-rede: " . $e->getMessage();
    }
}

header("Location: index.php");
exit;
?>