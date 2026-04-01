<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Buscar dados da sub-rede
$stmt = $pdo->prepare("SELECT * FROM subredes WHERE id = ?");
$stmt->execute([$id]);
$subrede = $stmt->fetch();

if (!$subrede) {
    header("Location: index.php");
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    
    if (empty($nome)) {
        $erro = "Nome da sub-rede é obrigatório";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE subredes SET nome = ?, descricao = ? WHERE id = ?");
            $stmt->execute([$nome, $descricao, $id]);
            $sucesso = "Sub-rede atualizada com sucesso!";
            
            // Atualizar dados locais
            $subrede['nome'] = $nome;
            $subrede['descricao'] = $descricao;
        } catch (PDOException $e) {
            $erro = "Erro ao atualizar sub-rede: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Sub-rede - IP Grid</title>
    <link rel="stylesheet" href="../assets/dashboard.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">🌐 IP GRID IPAM</div>
            <div class="nav-links">
                <a href="../dashboard.php">Dashboard</a>
                <a href="index.php">Sub-redes</a>
                <a href="../ips/index.php">IPs</a>
                <a href="../index.php">Site</a>
                <div class="user-info">
                    <span class="user-name">👤 <?php echo htmlspecialchars($_SESSION["usuarioLogado"]); ?></span>
                    <a href="../action/logout.php" class="btn-logout">Sair</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Editar Sub-rede</h1>
            <a href="index.php" class="btn-secondary">← Voltar</a>
        </div>

        <?php if ($erro): ?>
            <div class="alert alert-error"><?php echo $erro; ?></div>
        <?php endif; ?>

        <?php if ($sucesso): ?>
            <div class="alert alert-success"><?php echo $sucesso; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nome">Nome da Sub-rede *</label>
                    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($subrede['nome']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="rede">Endereço de Rede</label>
                    <input type="text" value="<?php echo $subrede['rede']; ?>/<?php echo $subrede['mascara']; ?>" disabled>
                    <small style="color: #666;">A rede não pode ser alterada após a criação</small>
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao"><?php echo htmlspecialchars($subrede['descricao']); ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" onclick="window.location.href='index.php'" class="btn-secondary">Cancelar</button>
                    <button type="submit" class="btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>