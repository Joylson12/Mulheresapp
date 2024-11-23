<?php include("config.php");
    if(isset($_GET['token'])) {
        $token = $_GET['token'];
        $consulta = $MySQLi->query("SELECT tec_token, tec_token_usado, TIMEDIFF(NOW(), tec_token_data) as validade FROM tb_tecnicos where tec_token = '$token'");
        if(!$resultado = $consulta->fetch_assoc() or $resultado['validade']>'24:00:00' or $resultado['tec_token_usado']==1){
            header("Location: alterar_senha.php?msg=0");
        }
    }
	if(isset($_POST['token'])) {
	    $token = $_POST['token'];
	    $senha = $_POST['senha'];
	    $consulta2 = $MySQLi->query("SELECT tec_codigo FROM tb_tecnicos where tec_token = '$token'");
	    if($resultado2 = $consulta2->fetch_assoc()){
	        $tecnico = $resultado2['tec_codigo'];
	        $senha = md5($senha);
	        $consulta3 = $MySQLi->query("UPDATE tb_tecnicos SET tec_senha = '$senha', tec_token_usado = 1 WHERE tec_codigo = $tecnico");
	        header("Location: login.php?msg2=6");
	    } 
        else header("Location: alterar_senha.php?msg=0");
	    
	       
	}

    
?>

<!DOCTYPE html>
<html lang="pt-br">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <meta name="description" content="O MulheresApp é um software de cadastramento e acompanhamento de mulheres vítimas de violência doméstica, para secretarias de apoio à mulher.">
  <link rel="manifest" href="/manifest.json">
  <link rel="apple-touch-icon" href="imagens/logomc.png">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="theme-color" content="#2a0044"
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MulheresApp | Redefinição dde senha</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
 
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <script src="https://apis.google.com/js/platform.js" async defer></script>
  <meta name="google-signin-client_id" content="582244156383-8h7u6g4vh6sahpml3sb6i3f3hbvj4l57.apps.googleusercontent.com">
  <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
  <link rel="icon" type="image/png" href="imagens/logomc.png" sizes="310x310">
</head>
<body class="hold-transition login-page">
<div role="heading" aria-level="1" class="login-box">
  <div class="login-logo">
    <a href="index.php"><b>Mulheres</b>App</a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Redefina sua senha</p>

      <form role="form" action="?" method="post">
         <div id="alerta" style="display: none" class="alert alert-danger" role="alert">
                       As senhas não coincidem!
        </div>
        <!--<label  for="email">Email ou Matrícula</label> e <label for="senha">Senha</label><br>-->
        <div class="input-group mb-3">
          <input type="hidden" name="token" value="<?php echo $token ?>">
          <input name="senha" minlength = "8" type="password" id="senha" class="form-control" placeholder="Senha" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          
          <input id="confirmar" type="password" class="form-control" placeholder="Confirme sua senha" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row justify-content-md-center justify-content-sm-center justify-content-center">
          
          <!-- /.col -->
          <div class="col-4">
            <button role="button" onclick="return verifica()" type="submit" class="btn btn-primary btn-block">Salvar</button>
          </div>
          <!-- /.col -->
        </div>
      </form
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->


<script>
    function verifica(){
        var senha = document.getElementById('senha').value;
        var confirma = document.getElementById('confirmar').value;
        if(senha!=confirma){
          document.getElementById('alerta').style.display = 'block';
          return false;
        }else return true;
    }
</script>

<!-- jQuery -->
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>

</html>


<!-- InputMask -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
<script>
  $(function () {
    $('[data-mask]').inputmask();
  });
</script>
