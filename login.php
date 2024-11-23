<?php include("config.php");
    
	if(isset($_SESSION["id"])){
	    header("Location: index.php");
	}
 
	$success = null;

	if(isset($_POST['email'])) {
	    $email = $_POST['email'];
	    $senha = $_POST['senha'];
	    if($senha == null) $senha = "''";
	    $consulta = $MySQLi->query("SELECT tec_codigo, tec_apelido, tec_ativo, tec_admin FROM tb_tecnicos WHERE (tec_email = '$email' or tec_matricula = '$email') AND tec_senha = md5('$senha')");
	    if($resultado = $consulta->fetch_assoc()){
	        $codigo = $resultado['tec_codigo'];
	        if($senha == "''"){
	            header("Location: criarSenha.php?codigo=$codigo");  
	        } else {
	            if($resultado['tec_ativo'] == 0){
	               $msg2 = 5;
	            } else {
	                $_SESSION['id'] = $resultado['tec_codigo'];
	                $_SESSION['nome'] = $resultado['tec_apelido'];
	                $_SESSION['admin'] = $resultado['tec_admin'];
	                $_SESSION['foto'] =  'imagens/tecnicos/' . $_SESSION['id'] . '.jpg';
	                header("Location: index.php");
	            }
	        }
	    } else $msg = "Credenciais incorretas!";
	}
    if(isset($_GET['msg2'])) $msg2 = $_GET['msg2'];
    
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="description" content="O MulheresApp é um software de cadastramento e acompanhamento de mulheres vítimas de violência doméstica, para secretarias de apoio à mulher.">
    <link rel="manifest" href="manifest.json"/>
    <link rel="apple-touch-icon" href="imagens/logomc.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#2a0044">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>MulheresApp | Log in</title>
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
    <!-- Authentication with Google API -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <link rel="icon" type="image/png" href="imagens/logomc.png" sizes="310x310">
    
    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('sw.js')
            .then(function () {
                //console.log('service worker registered');
            })
            .catch(function () {
                console.warn('service worker failed');
            });
    }
    </script>
</head>
<body class="hold-transition login-page">
<div role="heading" aria-level="1" class="login-box">
  <div class="login-logo">
    <a href="index.php"><b>Mulheres</b>App</a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Faça login para iniciar sua sessão</p>

      <form id="form" role="form" action="?" method="post">
        <?php if(@$msg2 == 3) echo
                    "<div id='alerta2' class='alert alert-success' role='alert'>
                        Senha criada com sucesso!
                    </div>";
        ?>  
        <?php if(@$msg2 == 4) echo
                    "<div id='alerta2' class='alert alert-danger' role='alert'>
                        Você já possui uma senha! Para redefini-la clique em 'Esqueci minha senha'!
                    </div>";
        ?>  
        <?php if(@$msg2 == 5) echo
                    "<div id='alerta2' class='alert alert-danger' role='alert'>
                        Você está desabilitado para realizar login nesse sistema! Contate um técnico administrativo!
                    </div>";
        ?>  
        <?php if(@$msg2 == 6) echo
                    "<div id='alerta2' class='alert alert-success' role='alert'>
                        Sua senha foi atualizada com sucesso! Faça login no sistema!
                    </div>";
        ?>  
        <div id="alerta" style="display: none" class="alert alert-danger" role="alert">
            Email não cadastrado!
        </div>
        <div id="alerta5" style="display: none" class="alert alert-danger" role="alert">
           Você está desabilitado para realizar login nesse sistema! Contate um técnico administrativo!
        </div>
        <?php if(@$msg != '') echo
            "<div id='alerta2' class='alert alert-danger' role='alert'>
                $msg
            </div>";
        ?>
        <!--<label for="email">Email ou Matrícula</label> e <label for="senha">Senha</label><br>-->
        <div class="input-group mb-3">
          <input id="email" name="email" type="text" class="form-control" placeholder="Email ou matrícula" data-inputmask="'mask': ['99.999-9','*{1,30}[.*{1,30}][.*{1,30}][.*{1,30}]@*{1,30}[.*{2,6}][.*{1,2}]']" data-mask >
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input name="senha" type="password" id="senha" class="form-control" placeholder="Senha">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row justify-content-md-center justify-content-sm-center justify-content-center">
          <!-- /.col -->
          <!-- O CAPTCHA foi removido -->
          <!-- /.col -->
        </div>
        <div class="row justify-content-center" style="margin-top:10px; margin-bottom:10px">
          <!-- /.col -->
          <div class="col-4">
            <button role="button" type="submit" class="btn btn-primary btn-block">Entrar</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      <p style="text-align:center; margin:0"><small>- OU ENTRE COM O GMAIL -</small></p>
      <div class="d-flex justify-content-center">
        <div class="btn"style="display:block" id="buttonDiv"></div>  
      </div>
      <!-- /.social-auth-links -->

      <div class="d-flex justify-content-center">
        <small><a href="alterar_senha.php">Esqueci minha senha</a></small>
      </div>
      <!--<p class="mb-0">-->
      <!--  <a href="register.html" class="text-center">Como me Registro?</a>-->
      <!--</p>-->
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<script>
    function handleCredentialResponse(response){
      const data = jwt_decode(response.credential);
      //console.log(data);
      if(data.email != ''){
        var dados = {
          userID:data.sub,
          userName:data.name,
          userPicture:data.picture,
          userEmail:data.email
        };
        $.post('valida_google.php', dados, function(retorna){
              if(retorna == 0){
                  document.getElementById('alerta').style.display = 'block';
              } else if(retorna == 5) document.getElementById('alerta5').style.display = 'block';
              else{
                  window.location.href = 'index.php';
              }
            });
      } else {
        document.getElementById('alerta').style.display = 'block';
      }
    }
    window.onload = function (){
      google.accounts.id.initialize({
        client_id:"582244156383-8h7u6g4vh6sahpml3sb6i3f3hbvj4l57.apps.googleusercontent.com",
        callback: handleCredentialResponse
      });
      google.accounts.id.renderButton(
        document.getElementById("buttonDiv"),{
          theme: "filled_blue",
          size: "medium",
          text: "signin"
        }//customization attributes
      );
      google.accounts.id.prompt();
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
