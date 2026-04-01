<?php
require_once "includes/auth_check.php";
require_once "config/db.php";

// Buscar estatísticas do banco
$totalSubredes = $pdo->query("SELECT COUNT(*) FROM subredes")->fetchColumn();

$totalIPs = $pdo->query("SELECT COUNT(*) FROM ips")->fetchColumn();

$totalEmUso = $pdo->query("SELECT COUNT(*) FROM ips WHERE status = 'em_uso'")->fetchColumn();

$totalLivres = $pdo->query("SELECT COUNT(*) FROM ips WHERE status = 'livre'")->fetchColumn();

// Calcular percentual de ocupação
$ocupacao = ($totalIPs > 0) ? round(($totalEmUso / $totalIPs) * 100) : 0;

// Buscar alertas (sub-redes com mais de 80% de ocupação)
$alertas = $pdo->query("
    SELECT s.nome as subrede, COUNT(i.id) as total_ips, 
           SUM(CASE WHEN i.status = 'em_uso' THEN 1 ELSE 0 END) as em_uso,
           ROUND((SUM(CASE WHEN i.status = 'em_uso' THEN 1 ELSE 0 END) / COUNT(i.id)) * 100) as percentual
    FROM subredes s
    LEFT JOIN ips i ON s.id = i.subrede_id
    GROUP BY s.id
    HAVING percentual >= 80
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - IP Grid IPAM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }

        /* Navbar (similar ao index.php) */
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #555;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #667eea;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-name {
            color: #667eea;
            font-weight: bold;
        }

        .btn-logout {
            background: #e74c3c;
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 25px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            background: #c0392b;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            margin-top: 80px;
        }

        /* Header */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            color: #333;
            font-size: 2rem;
        }

        .page-header p {
            color: #666;
            margin-top: 0.5rem;
        }

        /* Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #888;
            font-size: 0.8rem;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }

        /* Seções */
        .section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .section-header h2 {
            color: #333;
            font-size: 1.3rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem;
        }

        /* Lista de sub-redes */
        .subnet-list {
            display: grid;
            gap: 1rem;
        }

        .subnet-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .subnet-item:hover {
            background: #e9ecef;
        }

        .subnet-info h4 {
            color: #333;
            margin-bottom: 0.3rem;
        }

        .subnet-info p {
            color: #666;
            font-size: 0.85rem;
        }

        .subnet-stats {
            text-align: right;
        }

        .subnet-stats .percent {
            font-weight: bold;
            color: #667eea;
        }

        /* Alertas */
        .alert-list {
            display: grid;
            gap: 0.8rem;
        }

        .alert-item {
            padding: 1rem;
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            border-radius: 8px;
        }

        .alert-item h4 {
            color: #e67e22;
            margin-bottom: 0.3rem;
        }

        .alert-item p {
            color: #666;
            font-size: 0.9rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #999;
        }

        .footer {
            text-align: center;
            padding: 2rem;
            color: #888;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">🌐 IP GRID IPAM</div>
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="subredes/index.php">Sub-redes</a>
                <a href="ips/index.php">IPs</a>
                <a href="index.php">Site</a>
                <div class="user-info">
                    <span class="user-name">👤 <?php echo htmlspecialchars($usuarioLogado); ?></span>
                    <a href="action/logout.php" class="btn-logout">Sair</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Dashboard</h1>
            <p>Bem-vindo ao sistema de gerenciamento IPAM. Visualize o status da sua rede.</p>
        </div>

        <!-- Cards de estatísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total de Sub-redes</h3>
                <div class="stat-number"><?php echo $totalSubredes; ?></div>
                <div class="stat-label">Redes cadastradas</div>
            </div>

            <div class="stat-card">
                <h3>Total de IPs</h3>
                <div class="stat-number"><?php echo $totalIPs; ?></div>
                <div class="stat-label">Endereços gerenciados</div>
            </div>

            <div class="stat-card">
                <h3>IPs em Uso</h3>
                <div class="stat-number"><?php echo $totalEmUso; ?></div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo $ocupacao; ?>%"></div>
                </div>
                <div class="stat-label"><?php echo $ocupacao; ?>% de ocupação</div>
            </div>

            <div class="stat-card">
                <h3>IPs Livres</h3>
                <div class="stat-number"><?php echo $totalLivres; ?></div>
                <div class="stat-label">Disponíveis para uso</div>
            </div>
        </div>

        <!-- Últimas sub-redes -->
        <div class="section">
            <div class="section-header">
                <h2>📡 Sub-redes Cadastradas</h2>
                <a href="subredes/criar.php" class="btn-primary">+ Nova Sub-rede</a>
            </div>
            
            <div class="subnet-list">
                <?php
                $subredes = $pdo->query("
                    SELECT s.*, COUNT(i.id) as total_ips,
                           SUM(CASE WHEN i.status = 'em_uso' THEN 1 ELSE 0 END) as em_uso
                    FROM subredes s
                    LEFT JOIN ips i ON s.id = i.subrede_id
                    GROUP BY s.id
                    LIMIT 5
                ")->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($subredes) > 0):
                    foreach ($subredes as $sub):
                        $percentual = ($sub['total_ips'] > 0) ? round(($sub['em_uso'] / $sub['total_ips']) * 100) : 0;
                ?>
                <div class="subnet-item">
                    <div class="subnet-info">
                        <h4><?php echo htmlspecialchars($sub['nome']); ?></h4>
                        <p><?php echo $sub['rede']; ?>/<?php echo $sub['mascara']; ?></p>
                    </div>
                    <div class="subnet-stats">
                        <div class="percent"><?php echo $percentual; ?>%</div>
                        <div class="stat-label"><?php echo $sub['em_uso']; ?>/<?php echo $sub['total_ips']; ?> IPs</div>
                        <a href="ips/index.php?subrede=<?php echo $sub['id']; ?>" class="btn-secondary" style="margin-top: 0.5rem; display: inline-block;">Ver IPs</a>
                    </div>
                </div>
                <?php 
                    endforeach;
                else:
                ?>
                <div class="empty-state">
                    <p>Nenhuma sub-rede cadastrada ainda.</p>
                    <a href="subredes/criar.php" class="btn-primary" style="margin-top: 1rem; display: inline-block;">Criar primeira sub-rede</a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Alertas -->
        <div class="section">
            <div class="section-header">
                <h2>⚠️ Alertas de Ocupação</h2>
            </div>
            
            <div class="alert-list">
                <?php if (count($alertas) > 0): ?>
                    <?php foreach ($alertas as $alerta): ?>
                    <div class="alert-item">
                        <h4>🔔 Sub-rede: <?php echo htmlspecialchars($alerta['subrede']); ?></h4>
                        <p>Ocupação de <?php echo $alerta['percentual']; ?>% (<?php echo $alerta['em_uso']; ?>/<?php echo $alerta['total_ips']; ?> IPs em uso)</p>
                        <p style="font-size: 0.85rem;">Recomenda-se planejar a expansão ou liberar IPs não utilizados.</p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <p>✅ Nenhum alerta ativo. Todas as sub-redes estão com ocupação saudável.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>IP Grid IPAM - Gerenciamento Inteligente de Endereços IP</p>
    </div>
</body>
</html>