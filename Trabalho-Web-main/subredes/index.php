<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

$subredes = $pdo->query("
    SELECT s.*, COUNT(i.id) as total_ips,
           SUM(CASE WHEN i.status = 'em_uso' THEN 1 ELSE 0 END) as em_uso
    FROM subredes s
    LEFT JOIN ips i ON s.id = i.subrede_id
    GROUP BY s.id
    ORDER BY s.criado_em DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sub-redes - IP Grid</title>
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
            <h1>Sub-redes</h1>
            <a href="criar.php" class="btn-primary">+ Nova Sub-rede</a>
        </div>

        <div class="subnet-grid">
            <?php foreach ($subredes as $sub): 
                $percentual = ($sub['total_ips'] > 0) ? round(($sub['em_uso'] / $sub['total_ips']) * 100) : 0;
                $statusClass = $percentual >= 80 ? 'warning' : ($percentual >= 50 ? 'info' : 'success');
            ?>
            <div class="subnet-card">
                <div class="subnet-header">
                    <h3><?php echo htmlspecialchars($sub['nome']); ?></h3>
                    <span class="status-badge <?php echo $statusClass; ?>"><?php echo $percentual; ?>%</span>
                </div>
                <div class="subnet-details">
                    <p><strong>Rede:</strong> <?php echo $sub['rede']; ?>/<?php echo $sub['mascara']; ?></p>
                    <p><strong>IPs:</strong> <?php echo $sub['em_uso']; ?>/<?php echo $sub['total_ips']; ?></p>
                    <?php if ($sub['descricao']): ?>
                        <p><strong>Descrição:</strong> <?php echo htmlspecialchars($sub['descricao']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill <?php echo $statusClass; ?>" style="width: <?php echo $percentual; ?>%"></div>
                </div>
                <div class="subnet-actions">
                    <a href="../ips/index.php?subrede=<?php echo $sub['id']; ?>" class="btn-sm">Ver IPs</a>
                    <a href="editar.php?id=<?php echo $sub['id']; ?>" class="btn-sm btn-edit">Editar</a>
                    <a href="excluir.php?id=<?php echo $sub['id']; ?>" class="btn-sm btn-delete" onclick="return confirm('Tem certeza que deseja excluir esta sub-rede? Todos os IPs associados serão deletados.')">Excluir</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>