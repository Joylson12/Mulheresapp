<?php include("config.php");
    if(isset($_POST['senha'])) {
        $id = $_POST['codigo'];
        $senha = $_POST['senha'];
        $consulta2 = $MySQLi->query("SELECT tec_senha FROM tb_tecnicos where tec_codigo = $id");
        $resultado = $consulta2->fetch_assoc();
        if($resultado['tec_senha'] == 'd41d8cd98f00b204e9800998ecf8427e'){
            $consulta= $MySQLi->query("UPDATE tb_tecnicos SET tec_senha = md5($senha) WHERE tec_codigo = $id");
            header("Location: login.php?msg2=3");
        }else header("Location: login.php?msg2=4");
    }
    if(isset($_GET['codigo'])){
      $codigo = $_GET['codigo'];  
    }else  header("Location: login.php"); 
?>

<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MulheresApp | Criar Senha</title>
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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <link rel="icon" type="image/png" href="imagens/logomc.png" sizes="310x310">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="icons.html"><b>Mulheres</b>App</a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Para sua segurança, crie uma senha</p>
      <p class="login-box-msg">A senha precisa ter no mínimo 8 dígitos.</p>
        
      <form action="?codigo=<?php echo $codigo?>" method="post">
        <div id="alerta" style="display: none" class="alert alert-danger" role="alert">
            As senhas não coincidem!
        </div>
        <input name="codigo" type="hidden" value="<?php echo $codigo?>">
        <div class="input-group mb-3">
          <input id="senha" name="senha" type="password" minlength = "8" class="form-control" placeholder="Senha">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input id="confirma" type="password" class="form-control" placeholder="Confirme sua senha">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row justify-content-md-center justify-content-sm-center justify-content-center">
          
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" onclick="return verifica()" class="btn btn-primary btn-block">Enviar</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<script>
    function verifica(){
        var senha = document.getElementById('senha').value;
        var confirma = document.getElementById('confirma').value;
        if(senha!=confirma){
          document.getElementById('alerta').style.display = 'block';
          return false;
        }else return true;
    }
</script>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

</body>
</html>
