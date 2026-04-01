<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

$subrede_id = isset($_GET['subrede']) ? (int)$_GET['subrede'] : 0;

// Buscar sub-redes para o filtro
$subredes = $pdo->query("SELECT id, nome, rede, mascara FROM subredes ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Buscar IPs da sub-rede selecionada
$ips = [];
$subrede_atual = null;

if ($subrede_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM subredes WHERE id = ?");
    $stmt->execute([$subrede_id]);
    $subrede_atual = $stmt->fetch();
    
    if ($subrede_atual) {
        $stmt = $pdo->prepare("SELECT * FROM ips WHERE subrede_id = ? ORDER BY INET_ATON(endereco)");
        $stmt->execute([$subrede_id]);
        $ips = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Processar ações (reservar/liberar IP)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip_id = (int)$_POST['ip_id'];
    $acao = $_POST['acao'];
    
    if ($acao == 'reservar') {
        $hostname = trim($_POST['hostname']);
        $responsavel = trim($_POST['responsavel']);
        $descricao = trim($_POST['descricao']);
        
        $stmt = $pdo->prepare("UPDATE ips SET status = 'em_uso', hostname = ?, responsavel = ?, descricao = ?, reservado_em = NOW() WHERE id = ?");
        $stmt->execute([$hostname, $responsavel, $descricao, $ip_id]);
        $_SESSION['mensagem'] = "IP reservado com sucesso!";
    } elseif ($acao == 'liberar') {
        $stmt = $pdo->prepare("UPDATE ips SET status = 'livre', hostname = NULL, responsavel = NULL, descricao = NULL, reservado_em = NULL WHERE id = ?");
        $stmt->execute([$ip_id]);
        $_SESSION['mensagem'] = "IP liberado com sucesso!";
    }
    
    header("Location: index.php?subrede=" . $subrede_id);
    exit;
}

$mensagem = isset($_SESSION['mensagem']) ? $_SESSION['mensagem'] : '';
$erro = isset($_SESSION['erro']) ? $_SESSION['erro'] : '';
unset($_SESSION['mensagem'], $_SESSION['erro']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar IPs - IP Grid</title>
    <link rel="stylesheet" href="../assets/dashboard.css">
    <style>
        .filtro-subrede {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .ip-status {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
        }
        
        .ip-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-reservar {
            background: #28a745;
        }
        
        .btn-liberar {
            background: #dc3545;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
        }
        
        .modal-content h3 {
            margin-bottom: 1rem;
        }
        
        .modal-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">🌐 IP GRID IPAM</div>
            <div class="nav-links">
                <a href="../dashboard.php">Dashboard</a>
                <a href="../subredes/index.php">Sub-redes</a>
                <a href="index.php">IPs</a>
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
            <h1>Gerenciar IPs</h1>
        </div>

        <?php if ($mensagem): ?>
            <div class="alert alert-success"><?php echo $mensagem; ?></div>
        <?php endif; ?>
        
        <?php if ($erro): ?>
            <div class="alert alert-error"><?php echo $erro; ?></div>
        <?php endif; ?>

        <div class="filtro-subrede">
            <h3>Selecione uma Sub-rede</h3>
            <select id="subrede_select" onchange="window.location.href='index.php?subrede=' + this.value" style="width: 100%; padding: 0.8rem; margin-top: 1rem;">
                <option value="">-- Selecione uma sub-rede --</option>
                <?php foreach ($subredes as $sub): ?>
                    <option value="<?php echo $sub['id']; ?>" <?php echo $subrede_id == $sub['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($sub['nome']); ?> (<?php echo $sub['rede']; ?>/<?php echo $sub['mascara']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if ($subrede_atual): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Endereço IP</th>
                            <th>Status</th>
                            <th>Hostname</th>
                            <th>Responsável</th>
                            <th>Descrição</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ips as $ip): ?>
                            <tr>
                                <td><strong><?php echo $ip['endereco']; ?></strong></td>
                                <td>
                                    <span class="ip-status status-<?php echo $ip['status']; ?>">
                                        <?php 
                                        switch($ip['status']) {
                                            case 'livre': echo '🟢 Livre'; break;
                                            case 'em_uso': echo '🔴 Em uso'; break;
                                            case 'reservado': echo '🟡 Reservado'; break;
                                            default: echo $ip['status'];
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($ip['hostname'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($ip['responsavel'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($ip['descricao'] ?? '-'); ?></td>
                                <td class="ip-actions">
                                    <?php if ($ip['status'] == 'livre'): ?>
                                        <button onclick="abrirModalReservar(<?php echo $ip['id']; ?>, '<?php echo $ip['endereco']; ?>')" class="btn-sm btn-reservar">Reservar</button>
                                    <?php elseif ($ip['status'] == 'em_uso'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="ip_id" value="<?php echo $ip['id']; ?>">
                                            <input type="hidden" name="acao" value="liberar">
                                            <button type="submit" class="btn-sm btn-liberar" onclick="return confirm('Tem certeza que deseja liberar este IP?')">Liberar</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($subrede_id == 0 && count($subredes) > 0): ?>
            <div class="empty-state">
                <p>Selecione uma sub-rede para visualizar os IPs.</p>
            </div>
        <?php elseif (count($subredes) == 0): ?>
            <div class="empty-state">
                <p>Nenhuma sub-rede cadastrada.</p>
                <a href="../subredes/criar.php" class="btn-primary">Criar primeira sub-rede</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal para reservar IP -->
    <div id="modalReservar" class="modal">
        <div class="modal-content">
            <h3>Reservar IP</h3>
            <form method="POST" action="">
                <input type="hidden" name="ip_id" id="modal_ip_id">
                <input type="hidden" name="acao" value="reservar">
                
                <div class="form-group">
                    <label>Endereço IP</label>
                    <input type="text" id="modal_ip_endereco" disabled style="background: #f5f5f5;">
                </div>
                
                <div class="form-group">
                    <label for="hostname">Hostname</label>
                    <input type="text" id="hostname" name="hostname" placeholder="Ex: servidor-web-01">
                </div>
                
                <div class="form-group">
                    <label for="responsavel">Responsável</label>
                    <input type="text" id="responsavel" name="responsavel" placeholder="Nome do responsável">
                </div>
                
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" placeholder="Descrição do uso deste IP"></textarea>
                </div>
                
                <div class="modal-buttons">
                    <button type="button" onclick="fecharModal()" class="btn-secondary">Cancelar</button>
                    <button type="submit" class="btn-primary">Reservar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalReservar(ipId, ipEndereco) {
            document.getElementById('modal_ip_id').value = ipId;
            document.getElementById('modal_ip_endereco').value = ipEndereco;
            document.getElementById('modalReservar').style.display = 'flex';
        }
        
        function fecharModal() {
            document.getElementById('modalReservar').style.display = 'none';
        }
        
        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            const modal = document.getElementById('modalReservar');
            if (event.target == modal) {
                fecharModal();
            }
        }
    </script>
</body>
</html>