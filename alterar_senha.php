<?php header('Content-Type: text/html; charset=UTF-8'); 
    include('mail/PHPMailer.php');
    include('mail/SMTP.php');
    include('mail/Exception.php');
    include('config.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    
   
    $mail = new PHPMailer();
    if(isset($_POST['email'])) {
        $email = $_POST['email'];
        try{
            $consulta = $MySQLi->query("SELECT tec_codigo FROM tb_tecnicos WHERE tec_email = '$email'");
            if($resultado = $consulta->fetch_assoc()){
                $id = $resultado['tec_codigo'];
                $token = bin2hex(random_bytes(20));
                $consulta2 = $MySQLi->query("UPDATE tb_tecnicos SET tec_token = '$token', tec_token_data = now(), tec_token_usado = 0 where tec_codigo = $id");
                $mail->isSMTP();
                $mail->CharSet = 'UTF-8';
                $mail->Host = 'mail.mulheres.app.br';
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'ssl';
                $mail->Username = 'mulheres@mulheres.app.br';
                $mail->Password = 'Natal2020';
                $mail->Port = 465;
                $mail->setFrom('mulheres@mulheres.app.br', 'MulheresApp');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Redefinição de senha';
                $mail->Body = 'Chegou o email de redefinição de senha do <strong>MulheresApp</strong><br>
                                <a href="mulheres.app.br/redefinir_senha.php?token=' . $token . '">Clique aqui</a>';
                $mail->AltBody = 'Chegou o email de redefinição de senha do MulheresApp';
                
                if($mail->send()){
                    $msg = 3;
                }else $msg=4;
            }else $msg = 2;
            
        }catch (Exception $e){
            $msg=4;
        }
    }
    if(isset($_GET['msg'])) $msg = 1;
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
  <title>MulheresApp | Redefinição de senha</title>
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
          <?php if(@$msg==1) echo
                    "<div id='alerta' class='alert alert-danger' role='alert'>
                        Token inválido! Solicite outro abaixo para redefinir sua senha!
                    </div>";
            ?>
            <?php if(@$msg==2) echo
                    "<div id='alerta' class='alert alert-danger' role='alert'>
                        Email não cadastrado!
                    </div>";
            ?>
            <?php if(@$msg==3) echo
                    "<div id='alerta' class='alert alert-success' role='alert'>
                        Email enviado! Verifique sua caixa de email, inclusive o SPAM!
                    </div>";
            ?>
            <?php if(@$msg==4) echo
                    "<div id='alerta' class='alert alert-danger' role='alert'>
                        Ocorreu um erro em nosso sistema! Tente novamente!
                    </div>";
            ?>
        <!--<label  for="email">Email ou Matrícula</label> e <label for="senha">Senha</label><br>-->
        <div class="input-group mb-3">
          <input id="email" name="email" type="text" class="form-control" placeholder="Email" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="row justify-content-center">
          
          <!-- /.col -->
          <div class="col-4">
            <button role="button" type="submit" class="btn btn-primary btn-block">Enviar</button>
          </div>
          <!-- /.col -->
        </div>
      </form
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->



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
