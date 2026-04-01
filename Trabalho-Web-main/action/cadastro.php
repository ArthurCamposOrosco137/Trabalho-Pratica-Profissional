<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>IP Grid - Cadastro</title>
  <link rel="stylesheet" href="../assets/auth.css">
</head>
<body>
  <div class="container">
    <div class="login-box">
      <h2>Cadastro</h2>

      <form action="cadastrar.php" method="POST" onsubmit="return validarCadastro()">
        <label for="novoUsuario">Usuário</label>
        <input type="text" id="novoUsuario" name="usuario" placeholder="Crie seu usuário">

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" placeholder="Digite seu e-mail">

        <label for="novaSenha">Senha</label>
        <input type="password" id="novaSenha" name="senha" placeholder="Crie sua senha">

        <label for="confirmarSenha">Confirmar senha</label>
        <input type="password" id="confirmarSenha" placeholder="Confirme sua senha">

        <button type="submit">Cadastrar</button>
      </form>

      <p class="cadastro-texto">
        Já tem conta?
        <a href="login.php">Voltar para login</a>
      </p>
    </div>

    <div class="info-box">
      <h1>IP GRID</h1>
      <div class="info-card">
        <h3>Cadastro Rápido</h3>
        <p>Crie sua conta de forma simples e acesse o sistema.</p>
      </div>
      <div class="info-card">
        <h3>Mais Segurança</h3>
        <p>Organize os dados da rede com mais confiabilidade.</p>
      </div>
      <div class="info-card">
        <h3>Controle Total</h3>
        <p>Visualize melhor sua infraestrutura e evite erros.</p>
      </div>
      <div class="info-card">
        <h3>Experiência Moderna</h3>
        <p>Interface prática para facilitar o uso no dia a dia.</p>
      </div>
    </div>
  </div>

  <script src="../assets/script.js"></script>
</body>
</html>