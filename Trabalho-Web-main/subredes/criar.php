<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $rede = trim($_POST['rede']);
    $mascara = (int)$_POST['mascara'];
    $descricao = trim($_POST['descricao']);
    
    // Validar campos
    if (empty($nome)) {
        $erro = "Nome da sub-rede é obrigatório";
    } elseif (empty($rede)) {
        $erro = "Endereço de rede é obrigatório";
    } elseif (!filter_var($rede, FILTER_VALIDATE_IP)) {
        $erro = "Endereço de rede inválido";
    } elseif ($mascara < 1 || $mascara > 32) {
        $erro = "Máscara CIDR inválida (1-32)";
    } else {
        try {
            // Inserir sub-rede
            $stmt = $pdo->prepare("INSERT INTO subredes (nome, rede, mascara, descricao) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $rede, $mascara, $descricao]);
            $subrede_id = $pdo->lastInsertId();
            
            // Gerar IPs automaticamente
            // Converter rede para número inteiro
            $ip_long = ip2long($rede);
            $hosts = pow(2, (32 - $mascara));
            $primeiro_ip = $ip_long + 1;
            $ultimo_ip = $ip_long + $hosts - 2;
            
            // Inserir IPs (pular rede e broadcast)
            $stmt_ip = $pdo->prepare("INSERT INTO ips (subrede_id, endereco, status) VALUES (?, ?, 'livre')");
            
            for ($i = $primeiro_ip; $i <= $ultimo_ip; $i++) {
                $ip_endereco = long2ip($i);
                $stmt_ip->execute([$subrede_id, $ip_endereco]);
            }
            
            $sucesso = "Sub-rede criada com sucesso! " . ($ultimo_ip - $primeiro_ip + 1) . " IPs gerados automaticamente.";
            
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $erro = "Esta rede já está cadastrada!";
            } else {
                $erro = "Erro ao criar sub-rede: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Sub-rede - IP Grid</title>
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
            <h1>Nova Sub-rede</h1>
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
                    <input type="text" id="nome" name="nome" placeholder="Ex: Rede Wi-Fi, VLAN 10, Servidores" required>
                </div>

                <div class="form-group">
                    <label for="rede">Endereço de Rede *</label>
                    <input type="text" id="rede" name="rede" placeholder="Ex: 192.168.1.0" required>
                    <small style="color: #666; display: block; margin-top: 0.3rem;">Informe o endereço de rede (ex: 192.168.1.0)</small>
                </div>

                <div class="form-group">
                    <label for="mascara">Máscara CIDR *</label>
                    <select id="mascara" name="mascara" required>
                        <option value="">Selecione a máscara</option>
                        <option value="24">/24 - 255.255.255.0 (254 hosts)</option>
                        <option value="25">/25 - 255.255.255.128 (126 hosts)</option>
                        <option value="26">/26 - 255.255.255.192 (62 hosts)</option>
                        <option value="27">/27 - 255.255.255.224 (30 hosts)</option>
                        <option value="28">/28 - 255.255.255.240 (14 hosts)</option>
                        <option value="29">/29 - 255.255.255.248 (6 hosts)</option>
                        <option value="30">/30 - 255.255.255.252 (2 hosts)</option>
                        <option value="16">/16 - 255.255.0.0 (65534 hosts)</option>
                        <option value="20">/20 - 255.255.240.0 (4094 hosts)</option>
                        <option value="22">/22 - 255.255.252.0 (1022 hosts)</option>
                        <option value="23">/23 - 255.255.254.0 (510 hosts)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" placeholder="Descreva a finalidade desta sub-rede..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" onclick="window.location.href='index.php'" class="btn-secondary">Cancelar</button>
                    <button type="submit" class="btn-primary">Criar Sub-rede</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>