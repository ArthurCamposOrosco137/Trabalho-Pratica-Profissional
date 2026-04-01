<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>IP Grid - Login</title>
  <link rel="stylesheet" href="../assets/auth.css">
</head>
<body>
  <div class="container">
    <div class="login-box">
      <h2>Login</h2>

      <form action="autenticar.php" method="POST" onsubmit="return validarLogin()">
        <label for="usuario">Usuário</label>
        <input type="text" id="usuario" name="usuario" placeholder="Digite seu usuário">

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" placeholder="Digite sua senha">

        <button type="submit">Entrar</button>
      </form>

      <p class="cadastro-texto">
        Não tem conta?
        <a href="cadastro.php">Cadastre-se</a>
      </p>
    </div>

    <div class="info-box">
      <h1>IP GRID</h1>
      <div class="info-card">
        <h3>Gerenciamento Inteligente</h3>
        <p>Substitui planilhas por um sistema centralizado de IPAM.</p>
      </div>
      <div class="info-card">
        <h3>Prevenção de Erros</h3>
        <p>Evita conflitos de IP e sobreposições automaticamente.</p>
      </div>
      <div class="info-card">
        <h3>Visualização da Rede</h3>
        <p>Mapa de calor que mostra a ocupação da rede.</p>
      </div>
      <div class="info-card">
        <h3>Preparado para o Futuro</h3>
        <p>Suporte completo para IPv6.</p>
      </div>
    </div>
  </div>

  <script src="../assets/script.js"></script>
</body>
</html>