<?php
session_start();
$usuarioLogado = isset($_SESSION["usuarioLogado"]) ? $_SESSION["usuarioLogado"] : null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>IP Grid - Gerenciamento Inteligente de IPs</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #333;
      line-height: 1.6;
    }

    .navbar {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
    }

    /* Remove qualquer pseudo-elemento que possa criar a bola cinza */
    .navbar::before,
    .navbar::after {
      display: none !important;
    }

    .nav-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
    }

    .logo {
      font-size: 1.5rem;
      font-weight: bold;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      white-space: nowrap;
    }

    .nav-links {
      display: flex;
      gap: 1.5rem;
      align-items: center;
      flex-wrap: wrap;
    }

    /* Remove qualquer marcador de lista */
    .nav-links li {
      list-style: none;
    }

    /* Links normais da navegação */
    .nav-links a:not(.btn-login):not(.btn-logout) {
      text-decoration: none;
      color: #555;
      font-weight: 500;
      transition: color 0.3s;
    }

    .nav-links a:not(.btn-login):not(.btn-logout):hover {
      color: #667eea;
    }

    .user-name {
      color: #667eea;
      font-weight: bold;
      background: rgba(102, 126, 234, 0.1);
      padding: 0.4rem 1rem;
      border-radius: 20px;
      font-size: 0.9rem;
    }

    /* Botão de Login - Sempre visível */
    .btn-login {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 0.6rem 1.5rem;
      border-radius: 25px;
      text-decoration: none;
      font-weight: 600;
      font-size: 0.95rem;
      box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
      transition: all 0.3s;
      display: inline-block;
      border: none;
      cursor: pointer;
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
      background: linear-gradient(135deg, #7c8ef0 0%, #8b5eb0 100%);
      color: white;
    }

    .btn-login:active {
      transform: translateY(0);
    }

    /* Botão de Sair */
    .btn-logout {
      background: #e74c3c;
      color: white;
      padding: 0.6rem 1.2rem;
      border-radius: 25px;
      font-weight: 600;
      font-size: 0.95rem;
      transition: all 0.3s;
      box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
      border: none;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
    }

    .btn-logout:hover {
      background: #c0392b;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(231, 76, 60, 0.4);
      color: white;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 2rem;
    }

    .hero {
      padding: 120px 0 80px;
      text-align: center;
      color: white;
    }

    .hero h1 {
      font-size: 3rem;
      margin-bottom: 1rem;
      animation: fadeInUp 0.8s ease;
    }

    .hero p {
      font-size: 1.2rem;
      margin-bottom: 2rem;
      opacity: 0.9;
      animation: fadeInUp 0.8s ease 0.2s both;
    }

    .hero-buttons {
      display: flex;
      gap: 1rem;
      justify-content: center;
      animation: fadeInUp 0.8s ease 0.4s both;
      flex-wrap: wrap;
    }

    .btn-primary {
      background: white;
      color: #667eea;
      padding: 1rem 2rem;
      border-radius: 50px;
      text-decoration: none;
      font-weight: bold;
      transition: transform 0.3s, box-shadow 0.3s;
      display: inline-block;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .btn-secondary {
      background: transparent;
      color: white;
      padding: 1rem 2rem;
      border-radius: 50px;
      text-decoration: none;
      font-weight: bold;
      border: 2px solid white;
      transition: all 0.3s;
      display: inline-block;
    }

    .btn-secondary:hover {
      background: white;
      color: #667eea;
      transform: translateY(-2px);
    }

    .metrics {
      padding: 60px 0;
    }

    .metrics-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
      margin-top: -80px;
    }

    .metric-card {
      background: white;
      padding: 2rem;
      border-radius: 15px;
      text-align: center;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s;
      animation: fadeInUp 0.6s ease both;
    }

    .metric-card:hover {
      transform: translateY(-5px);
    }

    .metric-number {
      font-size: 2.5rem;
      font-weight: bold;
      color: #667eea;
      margin: 1rem 0;
    }

    .metric-label {
      color: #666;
      font-size: 0.9rem;
    }

    .progress-bar {
      width: 100%;
      height: 8px;
      background: #e0e0e0;
      border-radius: 10px;
      overflow: hidden;
      margin: 1rem 0;
    }

    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
      border-radius: 10px;
    }

    .features {
      padding: 80px 0;
      background: #f8f9fa;
    }

    .section-title {
      text-align: center;
      font-size: 2.5rem;
      margin-bottom: 3rem;
      color: #333;
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2rem;
    }

    .feature-card {
      background: white;
      padding: 2rem;
      border-radius: 15px;
      text-align: center;
      transition: all 0.3s;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }

    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .feature-icon {
      width: 80px;
      height: 80px;
      margin: 0 auto 1.5rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      color: white;
    }

    .feature-card h3 {
      margin-bottom: 1rem;
      color: #333;
    }

    .feature-card p {
      color: #666;
    }

    .network-viz {
      padding: 80px 0;
      background: white;
    }

    .network-container {
      background: #f8f9fa;
      border-radius: 15px;
      padding: 2rem;
      margin-top: 2rem;
    }

    .subnet-tree {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
      gap: 2rem;
    }

    .subnet-node {
      background: white;
      padding: 1rem;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      min-width: 200px;
    }

    .subnet-name {
      font-weight: bold;
      color: #667eea;
      margin-bottom: 1rem;
      text-align: center;
    }

    .ip-list {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      justify-content: center;
    }

    .ip-block {
      width: 35px;
      height: 35px;
      border-radius: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.7rem;
      font-weight: bold;
      cursor: pointer;
      transition: transform 0.2s;
    }

    .ip-block:hover {
      transform: scale(1.1);
    }

    .ip-used {
      background: #4caf50;
      color: white;
    }

    .ip-free {
      background: #e0e0e0;
      color: #666;
    }

    .ip-warning {
      background: #ff9800;
      color: white;
    }

    .about {
      padding: 80px 0;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .about-content {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 3rem;
      align-items: center;
    }

    .tech-icons {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      margin-top: 1rem;
    }

    .tech-icon {
      background: rgba(255, 255, 255, 0.2);
      padding: 0.5rem 1rem;
      border-radius: 20px;
      font-size: 0.9rem;
    }

    .team-members {
      display: flex;
      justify-content: space-between;
      gap: 1rem;
      margin-top: 1rem;
      flex-wrap: nowrap;
    }

    .member {
      text-align: center;
      background: rgba(255, 255, 255, 0.1);
      padding: 1.2rem 0.8rem;
      border-radius: 12px;
      transition: transform 0.3s, background 0.3s;
      backdrop-filter: blur(10px);
      flex: 1;
      min-width: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .member:hover {
      transform: translateY(-5px);
      background: rgba(255, 255, 255, 0.2);
    }

    .member-avatar {
      width: 70px;
      height: 70px;
      background: rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      margin: 0 auto 0.8rem;
      flex-shrink: 0;
    }

    .member-name {
      font-weight: bold;
      font-size: 0.9rem;
      margin-bottom: 0.3rem;
      color: white;
      line-height: 1.3;
      text-align: center;
      width: 100%;
    }

    .member-role {
      font-size: 0.75rem;
      opacity: 0.8;
      color: white;
    }

    .footer {
      background: #1a1a2e;
      color: white;
      padding: 3rem 0 1rem;
    }

    .footer-content {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
      margin-bottom: 2rem;
    }

    .footer-section h4 {
      margin-bottom: 1rem;
    }

    .footer-section a {
      color: #ccc;
      text-decoration: none;
      display: block;
      margin-bottom: 0.5rem;
      transition: color 0.3s;
    }

    .footer-section a:hover {
      color: #667eea;
    }

    .footer-bottom {
      text-align: center;
      padding-top: 2rem;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 900px) {
      .about-content {
        grid-template-columns: 1fr;
        text-align: center;
      }

      .tech-icons {
        justify-content: center;
      }

      .team-members {
        flex-wrap: wrap;
      }

      .member {
        min-width: 140px;
      }
    }

    @media (max-width: 768px) {
      .nav-container {
        flex-direction: column;
      }

      .hero h1 {
        font-size: 2rem;
      }

      .hero p {
        font-size: 1rem;
      }

      .hero-buttons {
        flex-direction: column;
        align-items: center;
      }

      .nav-links {
        gap: 1rem;
        justify-content: center;
      }
    }

    @media (max-width: 600px) {
      .team-members {
        overflow-x: auto;
        padding-bottom: 0.5rem;
        gap: 0.8rem;
        flex-wrap: nowrap;
      }

      .member {
        min-width: 130px;
        flex: none;
        padding: 1rem;
      }

      .btn-login, .btn-logout {
        padding: 0.5rem 1.2rem;
        font-size: 0.85rem;
      }
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="nav-container">
      <div class="logo">🌐 IP GRID</div>

      <div class="nav-links">
        <a href="#home">Início</a>
        <a href="#features">Funcionalidades</a>
        <a href="#network">Rede</a>
        <a href="#about">Sobre</a>

        <span id="userDisplay" class="user-name"></span>
        <a id="loginLink" href="action/login.php" class="btn-login">Entrar</a>
        <button id="logoutBtn" class="btn-logout" style="display: none;">Sair</button>
      </div>
    </div>
  </nav>

  <section id="home" class="hero">
    <div class="container">
      <h1>Gerencie sua Rede com Inteligência</h1>
      <p>Centralize, organize e monitore toda a infraestrutura de IPs da sua organização em um só lugar.</p>
      <div class="hero-buttons">
    <?php if (isset($_SESSION["usuarioLogado"])): ?>
        <a href="dashboard.php" class="btn-primary">🚀 Acessar Dashboard</a>
    <?php else: ?>
        <a href="action/login.php" class="btn-primary">🔐 Fazer Login</a>
    <?php endif; ?>
    <a href="#network" class="btn-secondary">📊 Ver Demonstração</a>
</div>
    </div>
  </section>

  <section class="metrics">
    <div class="container">
      <div class="metrics-grid">
        <div class="metric-card" style="animation-delay: 0.1s">
          <div class="feature-icon" style="width: 50px; height: 50px; margin: 0 auto 1rem; font-size: 1.5rem;">📊</div>
          <div class="metric-number">2,547</div>
          <div class="metric-label">Total de IPs Gerenciados</div>
        </div>

        <div class="metric-card" style="animation-delay: 0.2s">
          <div class="feature-icon" style="width: 50px; height: 50px; margin: 0 auto 1rem; font-size: 1.5rem;">✅</div>
          <div class="metric-number">1,732</div>
          <div class="metric-label">IPs em Uso</div>
          <div class="progress-bar">
            <div class="progress-fill" style="width: 68%"></div>
          </div>
          <div class="metric-label">68% de ocupação</div>
        </div>

        <div class="metric-card" style="animation-delay: 0.3s">
          <div class="feature-icon" style="width: 50px; height: 50px; margin: 0 auto 1rem; font-size: 1.5rem;">🌐</div>
          <div class="metric-number">24</div>
          <div class="metric-label">Sub-redes Cadastradas</div>
        </div>

        <div class="metric-card" style="animation-delay: 0.4s">
          <div class="feature-icon" style="width: 50px; height: 50px; margin: 0 auto 1rem; font-size: 1.5rem;">⚠️</div>
          <div class="metric-number">3</div>
          <div class="metric-label">Alertas Ativos</div>
        </div>
      </div>
    </div>
  </section>

  <section id="features" class="features">
    <div class="container">
      <h2 class="section-title">Funcionalidades Principais</h2>
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">🔍</div>
          <h3>Descoberta de Rede</h3>
          <p>Escaneie sua rede automaticamente e descubra dispositivos novos em tempo real.</p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">📝</div>
          <h3>Reservas e DHCP</h3>
          <p>Gerencie reservas de IP e integre-se com seu servidor DHCP facilmente.</p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">🌍</div>
          <h3>DNS Integrado</h3>
          <p>Associe IPs a nomes de domínio de forma intuitiva e organizada.</p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">🏗️</div>
          <h3>Organização por Sub-redes</h3>
          <p>Visualize suas sub-redes IPv4 e IPv6 de forma hierárquica e intuitiva.</p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">🔒</div>
          <h3>Controle de Acesso</h3>
          <p>Diferencie permissões de administradores, operadores e usuários.</p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">📈</div>
          <h3>Relatórios</h3>
          <p>Gere relatórios detalhados de auditoria e utilização da rede.</p>
        </div>
      </div>
    </div>
  </section>

  <section id="network" class="network-viz">
    <div class="container">
      <h2 class="section-title">Visualização da Rede</h2>
      <p style="text-align: center; margin-bottom: 2rem; color: #666;">Mapa de calor das sub-redes - Ocupação em tempo real</p>

      <div class="network-container">
        <div class="subnet-tree">
          <div class="subnet-node">
            <div class="subnet-name">10.0.1.0/24</div>
            <div class="ip-list">
              <div class="ip-block ip-used">.1</div>
              <div class="ip-block ip-used">.2</div>
              <div class="ip-block ip-free">.3</div>
              <div class="ip-block ip-used">.4</div>
              <div class="ip-block ip-warning">.5</div>
              <div class="ip-block ip-free">.6</div>
              <div class="ip-block ip-used">.7</div>
              <div class="ip-block ip-used">.8</div>
            </div>
            <div class="progress-bar" style="margin-top: 1rem;">
              <div class="progress-fill" style="width: 62%"></div>
            </div>
          </div>

          <div class="subnet-node">
            <div class="subnet-name">10.0.2.0/24</div>
            <div class="ip-list">
              <div class="ip-block ip-used">.1</div>
              <div class="ip-block ip-used">.2</div>
              <div class="ip-block ip-used">.3</div>
              <div class="ip-block ip-used">.4</div>
              <div class="ip-block ip-free">.5</div>
              <div class="ip-block ip-free">.6</div>
              <div class="ip-block ip-used">.7</div>
              <div class="ip-block ip-free">.8</div>
            </div>
            <div class="progress-bar" style="margin-top: 1rem;">
              <div class="progress-fill" style="width: 50%"></div>
            </div>
          </div>

          <div class="subnet-node">
            <div class="subnet-name">192.168.1.0/24</div>
            <div class="ip-list">
              <div class="ip-block ip-used">.1</div>
              <div class="ip-block ip-used">.2</div>
              <div class="ip-block ip-used">.3</div>
              <div class="ip-block ip-warning">.4</div>
              <div class="ip-block ip-used">.5</div>
              <div class="ip-block ip-used">.6</div>
              <div class="ip-block ip-used">.7</div>
              <div class="ip-block ip-free">.8</div>
            </div>
            <div class="progress-bar" style="margin-top: 1rem;">
              <div class="progress-fill" style="width: 87%"></div>
            </div>
          </div>
        </div>

        <p style="text-align: center; margin-top: 2rem; color: #888; font-size: 0.9rem;">
          🟢 Em uso | ⚪ Disponível | 🟠 Próximo ao esgotamento
        </p>
      </div>
    </div>
  </section>

  <section id="about" class="about">
    <div class="container">
      <div class="about-content">
        <div>
          <h2 style="margin-bottom: 1rem;">Sobre o IP Grid</h2>
          <p style="margin-bottom: 1rem;">Projeto desenvolvido para a disciplina de Prática Profissional em Programação Web com o objetivo de criar uma ferramenta eficiente para o gerenciamento de endereços IP.</p>
          <p>Substitua planilhas por um sistema centralizado, evite conflitos e tenha visibilidade total da sua infraestrutura de rede.</p>

          <div class="tech-icons">
            <span class="tech-icon">HTML5</span>
            <span class="tech-icon">CSS3</span>
            <span class="tech-icon">JavaScript</span>
            <span class="tech-icon">React</span>
            <span class="tech-icon">Node.js</span>
            <span class="tech-icon">MySQL</span>
          </div>
        </div>

        <div>
          <h3 style="margin-bottom: 1rem;">Equipe de Desenvolvimento</h3>
          <div class="team-members">
            <div class="member">
              <div class="member-avatar">👨‍💻</div>
              <div class="member-name">Arthur Campos</div>
              <div class="member-role">Desenvolvedor</div>
            </div>

            <div class="member">
              <div class="member-avatar">👨‍💻</div>
              <div class="member-name">Gustavo Pereira</div>
              <div class="member-role">Desenvolvedor</div>
            </div>

            <div class="member">
              <div class="member-avatar">👨‍💻</div>
              <div class="member-name">Ian Luca</div>
              <div class="member-role">Product Owner</div>
            </div>

            <div class="member">
              <div class="member-avatar">👨‍💻</div>
              <div class="member-name">Gabriel Wellington</div>
              <div class="member-role">Scrum Master</div>
            </div>

            <div class="member">
              <div class="member-avatar">👨‍💻</div>
              <div class="member-name">Gabriel Felipe</div>
              <div class="member-role">Desenvolvedor</div>
            </div>
          </div>

          <div style="margin-top: 2rem;">
            <h3 style="margin-bottom: 0.5rem;">Preparado para o Futuro</h3>
            <p>✅ Suporte completo para IPv6</p>
            <p>✅ Escalabilidade para redes empresariais</p>
            <p>✅ API aberta para integrações</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <div class="footer-section">
          <h4>IP Grid</h4>
          <p>Gerenciamento inteligente de endereços IP para sua infraestrutura de rede.</p>
        </div>

        <div class="footer-section">
          <h4>Links Rápidos</h4>
          <a href="#home">Início</a>
          <a href="#features">Funcionalidades</a>
          <a href="#network">Rede</a>
          <a href="#about">Sobre</a>
        </div>

        <div class="footer-section">
          <h4>Documentação</h4>
          <a href="#">Manual do Usuário</a>
          <a href="#">API Reference</a>
          <a href="#">Guia de Instalação</a>
        </div>

        <div class="footer-section">
          <h4>Contato</h4>
          <a href="#">ipgrid@faculdade.edu.br</a>
          <a href="#">GitHub do Projeto</a>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; 2026 IP Grid - Trabalho Acadêmico de Prática Profissional em Programação Web</p>
      </div>
    </div>
  </footer>

  <script>
    // Dados do PHP para o JavaScript
    const usuarioLogadoPHP = <?php echo json_encode($usuarioLogado); ?>;
    
    // Smooth scroll para os links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });

    // Animações dos cards
    window.addEventListener('load', () => {
      const cards = document.querySelectorAll('.metric-card, .feature-card');
      cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
          card.style.transition = 'all 0.6s ease';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 100);
      });
      
      // Verificar se usuário está logado
      verificarLogin();
    });

    // Função para verificar estado do login e atualizar a interface
    function verificarLogin() {
      // Agora usa a variável PHP em vez do localStorage
      const usuarioLogado = usuarioLogadoPHP;
      const userDisplay = document.getElementById("userDisplay");
      const loginLink = document.getElementById("loginLink");
      const logoutBtn = document.getElementById("logoutBtn");

      if (usuarioLogado && usuarioLogado !== "") {
        userDisplay.textContent = "👤 " + usuarioLogado;
        userDisplay.style.display = "inline-block";
        loginLink.style.display = "none";
        logoutBtn.style.display = "inline-block";
      } else {
        userDisplay.style.display = "none";
        loginLink.style.display = "inline-block";
        logoutBtn.style.display = "none";
      }
    }

    // Função para fazer logout
    function fazerLogout() {
      // Redirecionar para um script PHP que faz logout
      window.location.href = "action/logout.php";
    }

    // Adicionar evento de logout
    const logoutBtn = document.getElementById("logoutBtn");
    if (logoutBtn) {
      logoutBtn.addEventListener("click", fazerLogout);
    }
</script>
</body>
</html>