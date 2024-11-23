<?php
include("config.php");
include("acesso.php");
// Variáveis para definição antes de incluir o design1.php class="nav-link active"

$design_titulo = "Novo Abrigamento";
$design_ativo = "m7"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Mulher";
$design_migalha1_link = "mulher.php";
$design_migalha2_texto = "Novo Abrigamento";
$design_migalha2_link = "";

?>

<?php include("design1.php");
    if(isset($_POST['mulher'])){
        $mulher = $_POST['mulher'];
        $inicio = $_POST['inicio'];
        $tecnico = $_POST['tecnico'];
        $pertences = $_POST['pertences'];
        $consulta = $MySQLi->query("INSERT INTO tb_abrigamentos (abr_data_inicio, abr_mul_codigo, abr_tec_codigo, abr_roldepertences) 
                                VALUES ('$inicio', $mulher, $tecnico, '$pertences')");
        header("Location: abrigo.php?msg=1");
    }else{
    
        if(isset($_GET['mulher'])){
            $mulher = $_GET['mulher'];
            $consulta2 = $MySQLi->query("SELECT mul_codigo, mul_nome FROM tb_mulheres WHERE mul_codigo = $mulher");
            $resultado2 = $consulta2->fetch_assoc();
        }else  header("Location: mulheres.php");
    }
?>


    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        
        <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Cadastrar novo abrigamento</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" action="?" method="POST">
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Nome</label>
                    <input type="hidden" name="mulher" value="<?php echo $resultado2['mul_codigo'] ?>">
                    <input type="text" class="form-control" value="<?php echo $resultado2['mul_nome'] ?>" disabled>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Data inicial</label>
                        <input name="inicio" required type="datetime-local" class="form-control" value="<?php echo date('Y-m-d') . 'T' . date('H:i')?>">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Técnico</label>
                        <input type="hidden" name="tecnico" value="<?php echo $_SESSION['id']?>">
                        <input type="text" class="form-control" value="<?php echo $_SESSION['nome'] ?>" disabled>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Pertences</label>
                    <textarea class="textarea" name="pertences" style="width: 100%; height: 200px; font-size: 14px;
                            line-height: 18px; border: 1px solid #dddddd; padding: 10px;" id="mytextarea"></textarea>
                  </div>
                  
                <div class="card-footer">
                  <center><button type="submit" class="btn btn-primary">Cadastrar</button></center>
                </div>
                    
                </div>
                <!-- /.card-body -->
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