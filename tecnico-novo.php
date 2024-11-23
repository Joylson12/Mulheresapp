<?php
include("config.php");
include("acesso.php");
include("permisaoAdm.php");
// Variáveis para definição antes de incluir o design1.php class="nav-link active"

$design_titulo = "Novo Técnico";
$design_ativo = "m1"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Gerenciar Técnicos";
$design_migalha1_link = "tecnicos_adm.php";
$design_migalha2_texto = "Novo Técnico";
$design_migalha2_link = "";

?>

<?php include("design1.php");
    if(isset($_POST['nome'])){
        $nome = $_POST['nome'];
        $apelido = $_POST['apelido'];
        $matricula = $_POST['matricula'];
        $lotacao = $_POST['lotacao'];
        $cargo = $_POST['cargo'];
        $senha = $_POST['senha'];

        $hashed = password_hash($senha, PASSWORD_DEFAULT);//problema aqui

        if(isset($_POST['admin'])) 
          $admin = 1;
        else 
          $admin = 0;
        if(isset($_POST['atende'])) 
          $atende = 1;
        else 
          $atende = 0;

        if (strpos($matricula, '@') != false) {
          $consulta = $MySQLi->query("INSERT INTO tb_tecnicos (tec_nome, tec_apelido, tec_lot_codigo, tec_car_codigo, tec_admin, tec_atende, tec_senha, tec_email) 
                                      VALUES ('$nome', '$apelido', $lotacao, $cargo, $admin, $atende, '$hashed', '$matricula')");

        }else{
          $consulta = $MySQLi->query("INSERT INTO tb_tecnicos (tec_nome, tec_apelido, tec_lot_codigo, tec_car_codigo, tec_admin, tec_atende, tec_senha, tec_matricula) 
                                      VALUES ('$nome', '$apelido', $lotacao, $cargo, $admin, $atende, '$hashed', '$matricula')");
        }

        $id = mysqli_insert_id($MySQLi);
        $destino = 'imagens/tecnicos/' . $id . ".jpg";
        $cod = rand(1, 7);
        $foto = 'imagens/u' . $cod . '.jpg';
        copy($foto, $destino);
        header("Location: tecnicos_adm.php?msg=1");
    }
    $consulta2 = $MySQLi->query("SELECT * FROM tb_lotacoes");
    $consulta3 = $MySQLi->query("SELECT * FROM tb_cargos");
    
?>


    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        
        <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Cadastrar novo Técnico</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" action="?" method="POST">
                <div class="card-body">
                  
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Nome</label>
                        <input required name="nome" type="text" class="form-control" id="nome" placeholder="João da Silva Melo Alves">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                      <label for="exampleInputEmail1">Nome abreviado</label>
                        <input name="apelido" type="text" class="form-control" placeholder="João da Silva">
                      </div>
                    </div>
                  </div>

                  <div class="row"><!-- fazendo agora-->
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Senha</label>
                        <input name="senha" type="password" class="form-control" placeholder="Senha">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Matrícula ou email</label>
                        <input required name="matricula" type="text" class="form-control" placeholder="Email ou matrícula" data-inputmask="'mask': ['99.999-9','*{1,30}[.*{1,30}][.*{1,30}][.*{1,30}]@*{1,30}[.*{2,6}][.*{1,2}]']" data-mask >
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
                              <option value="<?php echo $resultado2['lot_codigo']; ?>"><?php echo $resultado2['lot_lotacao']; ?></option>
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
                                <option value="<?php echo $resultado3['car_codigo']; ?>"><?php echo $resultado3['car_cargo']; ?></option>
                                <?php } ?>
                            </select>
                          </div>
                    
                    
                    </div>
                  </div>
                    
                    
                  <div class="form-check">
                    <input name="admin" type="checkbox" class="form-check-input">
                    <label class="form-check-label" for="exampleCheck1"><b>Administrador</b> - habilite apenas para gestores de pessoal.</label>
                  </div>
                  <div class="form-check">
                    <input name="atende" type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label" for="exampleCheck1"><b>Atendimento</b> - habilite apenas para técnicos que realizam atendimentos.</label>
                  </div>
                </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Cadastrar</button>
                </div>
              </form>
            </div>
        
        
      </div><!-- /.container-fluid -->
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