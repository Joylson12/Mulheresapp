<?php
include("config.php");
include("acesso.php");
// Variáveis para definição antes de incluir o design1.php class="nav-link active"

$design_titulo = "Mensagens";
$design_ativo = "m9"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Mensagens";
$design_migalha1_link = "mensagens.php";
$design_migalha2_texto = "Nova Mensagem";
$design_migalha2_link = "";

?>

<?php include("design1.php");
    if(isset($_POST['titulo'])){
            $titulo = $_POST['titulo'];
            $destinatario = $_POST['destinatario'];
            $texto = $_POST['texto'];
            if($texto==''){
                header("Location: ?msg=1");  
            }else{ 
               //print_r($_POST);
                $consulta = $MySQLi->query("INSERT INTO tb_mensagens (men_titulo, men_tec_remetente, men_tec_destinatario, men_texto) 
                                    VALUES ('$titulo', $id, $destinatario, '".addslashes($texto)."')");
                header("Location: mensagens.php?caixa=1&msg=1");
               
            }
    }
    $consulta3 = $MySQLi->query("SELECT tec_codigo, tec_apelido FROM tb_tecnicos
                                WHERE tec_codigo <>$id");
    if(isset($_GET['msg'])) $msg = $_GET['msg'];
    if(isset($_GET['tecnico'])) $tecnico = $_GET['tecnico'];
    if(isset($_GET['mensagem'])){
        $mensagem = $_GET['mensagem'];
        $consulta2 = $MySQLi->query("SELECT * FROM tb_mensagens 
                                    JOIN tb_tecnicos ON men_tec_remetente = tec_codigo
                                    where men_codigo = $mensagem");
        $resultado2 = $consulta2->fetch_assoc();
        if($resultado2['men_tec_destinatario']!=$id){
            header("Location: mensagens.php?caixa=1&msg=4");
        }
    } 
?>
  <script src="plugins/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
  <script type="text/javascript">
  tinymce.init({
    selector: '#mytextarea',
    menubar: false,
    language: 'pt_BR',
  toolbar: 'undo redo bold italic alignleft aligncenter alignright bullist numlist outdent indent code'
  });
  </script>

<section class="content">
     <?php if(@$msg==1) echo
                    "<div id='alerta' class='alert alert-danger' role='alert'>
                        Não é possível enviar uma mensagem vazia!
                    </div>";
            ?>
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">
            <a href="mensagens.php" class="btn btn-primary btn-block mb-3">Voltar para Mensagens</a>

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Pastas</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body p-0">
                <ul class="nav nav-pills flex-column">
                  <li class="nav-item active">
                    <a href="mensagens.php" class="nav-link">
                      <i class="fas fa-inbox"></i> Caixa de Entrada
                      <span class="badge bg-primary float-right"><?php echo $nolidas['nolidas'] ?></span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="mensagens.php?caixa=1" class="nav-link">
                      <i class="far fa-envelope"></i> Enviados
                    </a>
                  </li>
                   <li class="nav-item">
                    <a href="mesangens.php?caixa=2" class="nav-link">
                      <i class="far fa-trash-alt"></i> Arquivados
                    </a>
                  </li>
                </ul>
              </div>
              <!-- /.card-body -->
            </div>
            
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title">Enviar nova Mensagem</h3>
              </div>
              <!-- /.card-header -->
              <form role="form" action="?" method="POST">
              <div class="card-body">
                <div class="form-group">
                  <label>Destinatário</label>
                            <select name="destinatario" class="custom-select">
                                <?php if(!isset($_GET['mensagem'])) {?>
                                <?php while ($resultado3 = $consulta3 -> fetch_assoc()) {?>
                                <option value="<?php echo $resultado3['tec_codigo']; ?>" <?php if($resultado3['tec_codigo']==$tecnico) echo 'selected';  ?>><?php echo $resultado3['tec_apelido']; ?></option>
                                <?php }}else{ ?>
                                        <option value="<?php echo $resultado2['men_tec_remetente']; ?>"><?php echo $resultado2['tec_apelido']; ?></option>
                                <?php } ?>
                                
                    </select>
                </div>
                <div class="form-group">
                  <?php if(isset($_GET['mensagem'])) {?>
                  <input type="text" name="titulo" class="form-control" value="RE: <?php echo $resultado2['men_titulo']?>" placeholder="Título">
                  <?php }else{ ?>
                  <input type="text" name="titulo" class="form-control" placeholder="Título" maxlength="90">
                  <?php } ?>
                </div>
                <div class="form-group">
                    <textarea class="textarea" name="texto" style="width: 100%; height: 200px; font-size: 14px;
                            line-height: 18px; border: 1px solid #dddddd; padding: 10px;" id="mytextarea"></textarea>
                </div>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <div class="float-right">
                  <button type="submit" class="btn btn-primary"><i class="far fa-envelope"></i> Enviar</button>
                </div>
                <button type="reset" class="btn btn-default"><i class="fas fa-times"></i> Descartar</button>
              </div>
             </form>
              <!-- /.card-footer -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>



<?php
include("design2.php");
?>