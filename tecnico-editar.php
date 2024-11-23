<?php
include("config.php");
include("acesso.php");
include("permisaoAdm.php");
// Variáveis para definição antes de incluir o design1.php class="nav-link active"

$design_titulo = "Editar Técnico";
$design_ativo = "m4b"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Gerenciar Técnicos";
$design_migalha1_link = "tecnicos_adm.php";
$design_migalha2_texto = "Editar Técnico";
$design_migalha2_link = "";

?>

<?php include("design1.php");
    if(isset($_GET['codigo'])){
        $id = $_GET['codigo'];
        $consulta = $MySQLi->query("SELECT tec_codigo, tec_email, tec_telefone, tec_nome, tec_apelido, tec_matricula, tec_lot_codigo, tec_car_codigo, tec_admin, tec_ativo, tec_atende FROM tb_tecnicos WHERE tec_codigo = $id");
        $resultado = $consulta->fetch_assoc();
    }else header("Location: tecnicos_adm.php");
    
    if(isset($_POST['password'])){
        $password = $_POST['password'];
        $id = $_SESSION['id'];
        $consulta4 = $MySQLi->query("SELECT tec_senha FROM tb_tecnicos WHERE tec_codigo = $id");
        $resultado4 = $consulta4->fetch_assoc();
        $codigo = $_POST['codigo'];
        if($resultado4['tec_senha']==md5($password)){
            $nome = $_POST['nome'];
            $apelido = $_POST['apelido'];
            $matricula = $_POST['matricula'];
            $lotacao = $_POST['lotacao'];
            $cargo = $_POST['cargo'];
            if(isset($_POST['admin'])) $admin = 1;
            else $admin = 0;
            if(isset($_POST['atende'])) $atende = 1;
            else $atende = 0;
            if(isset($_POST['ativo'])) $ativo = 0;
            else $ativo = 1;
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];
            $senha = $_POST['senha'];
            if($senha==null){
                $consulta5 = $MySQLi->query("UPDATE tb_tecnicos SET tec_nome = '$nome', tec_apelido = '$apelido', tec_matricula = '$matricula', tec_lot_codigo = $lotacao, 
                                        tec_car_codigo = $cargo, tec_admin = $admin, tec_atende = $atende, tec_ativo = $ativo, tec_email = '$email', tec_telefone = '$telefone' 
                                        WHERE tec_codigo = $codigo"); 
            }else{
                $senha = md5($senha);
                $consulta5 = $MySQLi->query("UPDATE tb_tecnicos SET tec_nome = '$nome', tec_apelido = '$apelido', tec_matricula = '$matricula', tec_lot_codigo = $lotacao, 
                                        tec_car_codigo = $cargo, tec_admin = $admin, tec_atende = $atende, tec_ativo = $ativo, tec_email = '$email', tec_telefone = '$telefone', 
                                        tec_senha = '$senha' WHERE tec_codigo = $codigo");
            }
            header("Location: tecnicos_adm.php?msg=2");
        }else{
            header("Location: tecnico-editar.php?codigo=$codigo&msg=1");
        } 
    }
    
    if(isset($_GET['msg'])) $msg=$_GET['msg'];
        
    $consulta2 = $MySQLi->query("SELECT * FROM tb_lotacoes");
    $consulta3 = $MySQLi->query("SELECT * FROM tb_cargos");
    
?>


    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
          <?php if(@$msg==1) echo
                    "<div id='alerta' class='alert alert-danger' role='alert'>
                        Confirmação de senha inválida!
                    </div>";
                 ?> 
        <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Editar Técnico</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" action="?" method="POST">
                <div class="card-body">
                  <input type="hidden" name="codigo" value="<?php echo $resultado['tec_codigo']?>">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Nome</label>
                    <input name="nome" value="<?php echo $resultado['tec_nome'] ?>" type="text" class="form-control" id="nome" placeholder="João da Silva Melo Alves">
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Nome abreviado</label>
                        <input name="apelido" value="<?php echo $resultado['tec_apelido'] ?>" type="text" class="form-control" placeholder="João da Silva">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Matrícula</label>
                        <input name="matricula" value="<?php echo $resultado['tec_matricula'] ?>" data-inputmask="'mask': ['99.999-9']" data-mask type="text" class="form-control" id="exampleInputEmail1" placeholder="12.345-6">
                      </div>
                    </div>
                  </div>
                  
                  
                  <div class="row">
                    <div class="col-sm-6">
                          <!-- select -->
                          <div class="form-group">
                            <label>Lotação</label>
                            <select name="lotacao" class="custom-select">
                              <?php while ($resultado2 = $consulta2 -> fetch_assoc()) {?>
                              <option value="<?php echo $resultado2['lot_codigo']; ?>" <?php if($resultado2['lot_codigo']==$resultado['tec_lot_codigo']) echo 'selected' ?>><?php echo $resultado2['lot_lotacao']; ?></option>
                              <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                          <!-- select -->
                          <div class="form-group">
                            <label>Cargo</label>
                            <select name="cargo" class="custom-select">
                                <?php while ($resultado3 = $consulta3 -> fetch_assoc()) {?>
                                <option value="<?php echo $resultado3['car_codigo']; ?>" <?php if($resultado3['car_codigo']==$resultado['tec_car_codigo']) echo 'selected' ?>><?php echo $resultado3['car_cargo']; ?></option>
                                <?php } ?>
                            </select>
                          </div>
                    </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-6">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Email</label>
                        <input name="email" value="<?php echo $resultado['tec_email'] ?>" type="email" class="form-control" id="exampleInputEmail1">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Telefone</label>
                        <input name="telefone" type="text" class="form-control" data-inputmask="'mask': ['(99) 9999-9999','(99) 9 9999-9999']" data-mask value="<?php echo $resultado['tec_telefone']?>" placeholder="Seu telefone (para a equipe)">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Nova senha</label>
                        <input name="senha" type="password" class="form-control" minlength="8">
                      </div>
                    </div>
                  </div>
                    
                    
                  <div class="form-check">
                    <input name="admin" type="checkbox" class="form-check-input" id="exampleCheck1" <?php if($resultado['tec_admin']==1) echo 'checked'?>>
                    <label class="form-check-label" for="exampleCheck1"><b>Administrador</b> - habilite apenas para gestores de pessoal.</label>
                  </div>
                  <div class="form-check">
                    <input name="atende" type="checkbox" class="form-check-input" id="exampleCheck2" <?php if($resultado['tec_atende']==1) echo 'checked'?>>
                    <label class="form-check-label" for="exampleCheck2"><b>Atendimento</b> - habilite apenas para técnicos que realizam atendimentos.</label>
                  </div>
                  <div class="form-check">
                    <input name="ativo" type="checkbox" class="form-check-input" id="exampleCheck3" <?php if($resultado['tec_ativo']==0) echo 'checked'?>>
                    <label class="form-check-label" for="exampleCheck3"><b>Desabilitado</b> - marque esta opção para desativar este profissional. Ele não conseguirá mais logar no sistema.</label>
                  </div>
                </div>
                <!-- /.card-body -->
                
              
                <div class="card-footer justify-content-md-center text-center">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-senha">
                      Salvar Alterações
                    </button>
                </div>
                
                
                </div>
                
                <div class="modal fade" id="modal-senha">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title">Confirmação de senha</h4>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <input type="password" name="password" class="form-control" id="exampleInputEmail1" placeholder="Informe sua senha para continuar">
                    </div>
                    <div class="modal-footer justify-content-center">
                      <button type="submit" class="btn btn-primary">Confirmar</button>
                    </div>
                  </div>
                  <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
              </div>
              
              </form>
            </div>
        
        
     <!-- /.container-fluid -->
    </section>
    
    

<?php
include("design2.php");
?>
<script src="plugins/jquery/jquery.min.js"></script>
<!-- InputMask -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
<script>
  $(function () {
    $('[data-mask]').inputmask();
  });
</script>