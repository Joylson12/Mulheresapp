<?php
require_once('includes/config.php'); // Carregar o arquivo de configuração com os dois bancos de dados

$id = $_SESSION['id']; // Obtém o ID do usuário logado

// Consulta ao banco mulheresapp_natal para contar mensagens não lidas
$naolidas = $mysqli_natal->query("SELECT COUNT(men_codigo) as nolidas FROM tb_mensagens WHERE men_tec_destinatario = $id AND men_lida = 0");
$nolidas = $naolidas ? $naolidas->fetch_assoc() : ['nolidas' => 0];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="description"
    content="O MulheresApp é um software de cadastramento e acompanhamento de mulheres vítimas de violência doméstica, para secretarias de apoio à mulher.">
  <meta name="mobile-web-app-capable" content="yes">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MulheresApp | <?php echo $design_titulo; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <link rel="icon" type="image/png" href="../imagens/logomc.png" sizes="310x310">
  <meta name="google-signin-client_id" content="seu-google-client-id">
  <style>
    .main-sidebar {
      height: 100%;
      /* Garante que a sidebar ocupe 100% da altura */
    }
  </style>
</head>
</head>

<body class="hold-transition sidebar-mini" onLoad="initVars();">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="../index.php" class="nav-link">Início</a>
        </li>
      </ul>

      <!-- SEARCH FORM -->
      <form class="form-inline ml-3" action="/mulheres.php" method="post">
        <div class="input-group input-group-sm">
          <input class="form-control form-control-navbar" type="search" name="buscar" placeholder="Buscar Mulher"
            aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-navbar" type="submit">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
      </form>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Messages Dropdown Menu -->
        <li class="nav-item">
          <a class="nav-link" href="../mensagens.php">
            <i class="nav-icon fas fa-comments"></i>
            <?php if ($nolidas['nolidas'] > 0) { ?>
              <span class="badge badge-danger navbar-badge"><?php echo $nolidas['nolidas']; ?></span>
            <?php } ?>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../configuracoes.php">
            <i class="nav-icon fas fa-cogs"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../sair.php" onclick="signOut();">
            <i class="nav-icon fas fa-sign-out-alt"></i>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->
  </div>


  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="../index.php" class="brand-link">
      <img src="../imagens/logomc.png" alt="Logo MulheresApp" class="brand-image img-circle elevation-3">
      <span class="brand-text font-weight-light"><b>Mulheres</b>App</span>
    </a>

    <!-- Sidebar content -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../imagens/tecnicos/<?php echo $_SESSION['id']; ?>.jpg" class="img-circle elevation-2"
            alt="User Image" onClick="window.location='configuracoes.php'">
        </div>
        <div class="info">
          <a href="configuracoes.php" class="d-block"><?php echo $_SESSION['nome']; ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Links with icons -->
          <li class="nav-item">
            <a href="../" class="nav-link <?php if ($design_ativo == 'r1')
              echo 'active'; ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Voltar para Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="index.php" class="nav-link <?php if ($design_ativo == 'r2')
              echo 'active'; ?>">
              <i class="nav-icon fas fa-chart-bar"></i>
              <p>Página Inicial</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="selectTables.php" class="nav-link <?php if ($design_ativo == 'r3')
              echo 'active'; ?>">
              <i class="nav-icon fas fa-sliders-h"></i>
              <p>Configurar relatório</p>
            </a>
          </li>

          <li class="nav-header">RELATÓRIOS CADASTRADOS</li>
          <?php
          $query_recReports22 = "SELECT * FROM tblreports WHERE status = 0 ORDER BY txtReportName";
          $recReports22 = $mysqli_relatorios->query($query_recReports22);

          if ($recReports22) {
            while ($row_recReports22 = $recReports22->fetch_assoc()) {
              echo '<li class="nav-item">';
              echo '<a class="nav-link ' . (isset($_SESSION['txtReportName']) && $_SESSION['txtReportName'] == $row_recReports22['txtReportName'] ? 'active' : '') . '" href="loadReport.php?id=' . $row_recReports22['id'] . '">';
              echo '<i class="nav-icon fas fa-circle"></i>';
              echo '<p>' . htmlspecialchars($row_recReports22['txtReportName']) . '</p>'; // Sanitização para evitar XSS
              echo '</a>';
              echo '</li>';
            }
          } else {
            echo '<li class="nav-item">Erro na consulta: ' . $mysqli_relatorios->error . '</li>'; // Exibe erro caso a consulta falhe
          }
          ?>

          <li class="nav-header">SISTEMA</li>
          <li class="nav-item">
            <a href="../mensagens.php" class="nav-link <?php if ($design_ativo == 'm9')
              echo 'active'; ?>">
              <i class="nav-icon fas fa-comments"></i>
              <p>Mensagens <?php if ($nolidas['nolidas'] > 0) { ?><span
                    class="badge badge-info right"><?php echo $nolidas['nolidas']; ?></span><?php } ?></p>
            </a>
          </li>
          <li class="nav-item">
            <a href="../configuracoes.php" class="nav-link <?php if ($design_ativo == 'm8')
              echo 'active'; ?>">
              <i class="nav-icon fas fa-cogs"></i>
              <p>Configurações</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="../sair.php" onclick="signOut();" class="nav-link">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>Sair</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  </div>

  <script>
    function signOut() {
      var auth2 = gapi.auth2.getAuthInstance();
      auth2.signOut().then(function () {
        console.log('User signed out.');
      });
    }

    function onLoad() {
      gapi.load('auth2', function () {
        gapi.auth2.init();
      });
    }
  </script>
  <script src="https://apis.google.com/js/platform.js?onload=onLoad" async defer></script>