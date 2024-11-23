<?php
include("config.php");
include("acesso.php");
include("permisaoAdm.php");
// Variáveis para definição antes de incluir o design1.php class="nav-link active"

$design_titulo = "Acompanhamento de Abrigamento";
$design_ativo = "m7"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Abrigo";
$design_migalha1_link = "abrigo.php";
$design_migalha2_texto = "Acompanhar";
$design_migalha2_link = "";

?>

<?php include("design1.php");
    if(isset($_POST['password'])){
        $tecnico = $_SESSION['id'];
        $password = $_POST['password'];
        $consulta4 = $MySQLi->query("SELECT tec_senha FROM tb_tecnicos WHERE tec_codigo = $tecnico");
        $resultado4 = $consulta4->fetch_assoc();
        if($resultado4['tec_senha']==md5($password)){
            $excluir = $_POST['excluir'];
            $consulta = $MySQLi->query("SELECT aco_tec_codigo FROM tb_acompanhamento_abrigamentos
                                    WHERE aco_codigo = $excluir");
            $resultado = $consulta->fetch_assoc();
            if($resultado['aco_tec_codigo'] == $tecnico){
                $consulta2 = $MySQLi->query("DELETE FROM tb_acompanhamento_abrigamentos
                                        WHERE aco_codigo = $excluir");
            }else $msg=2; 
    
        }else $msg=3;  
        header("Location: ?codigo=".$_GET['codigo']."&msg=".$msg);
    }
    if(isset($_GET['msg'])) $msg = $_GET['msg'];

    if(isset($_GET['codigo'])){
        $codigo = $_GET['codigo'];
        
        $consulta = $MySQLi->query("SELECT mul_nome, abr_data_inicio, abr_data_fim FROM tb_abrigamentos
                                JOIN tb_mulheres ON abr_mul_codigo = mul_codigo
                                WHERE abr_codigo = $codigo");
        $resultado = $consulta->fetch_assoc();
        $consulta2 = $MySQLi->query("SELECT aco_data, tec_apelido, aco_codigo, aco_relatorio, aco_tec_codigo FROM tb_acompanhamento_abrigamentos 
                                    JOIN tb_abrigamentos ON aco_abr_codigo = abr_codigo
                                   JOIN tb_mulheres ON abr_mul_codigo = mul_codigo
                                   JOIN tb_tecnicos ON aco_tec_codigo = tec_codigo
                                    WHERE abr_codigo = $codigo
                                    ORDER BY aco_data");
    }else header("Location: abrigo.php");
    
    if(isset($_POST['relatorio'])){
        $abrigamento  = $_POST['abrigamento'];
        $tecnico = $_SESSION['id'];
        $relatorio = $_POST['relatorio'];
        $data = $_POST['data'];
        if($relatorio==''){
            header("Location: ?codigo=$abrigamento&msg=1");  
        }else{ 
            $consulta2 = $MySQLi->query("INSERT INTO tb_acompanhamento_abrigamentos (aco_relatorio, aco_tec_codigo, aco_abr_codigo, aco_data)
                                    VALUES ('".addslashes($relatorio)."', $tecnico, $abrigamento, '$data')");
            header("Location: ?codigo=$abrigamento");
        }
        
    }

    if(isset($_GET['msg'])) $msg = $_GET['msg'];
    $tecnico = $_SESSION['id'];
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

    <!-- Main content -->
    <section class="content">
      <?php if(@$msg==2) echo
                    "<div id='alerta' class='alert alert-danger' role='alert'>
                        Você não tem permissão para excluir esse acompanhamento! Contate o seu autor!
                    </div>";
            ?>
        <?php if(@$msg==3) echo
                    "<div id='alerta' class='alert alert-danger' role='alert'>
                        Confirmação de senha inválida!
                    </div>";
            ?>
      <div class="container-fluid">
        
        
        <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Abrigada: <?php echo $resultado['mul_nome'] ?></h3>
              </div>
              
              <div class="card-body">
                  
              <div class="row">
          <div class="col-md-12">
            <!-- The time line -->
            <div class="timeline">
                
                
                <!-- timeline time label -->
              <div class="time-label">
                
                <span class="bg-green"><?php echo dataEmPortugues(strtotime($resultado['abr_data_inicio'])) ?></span>
              </div>
              <!-- /.timeline-label -->
              
              
              <!-- timeline item -->
              <div>
                <i class="fas fa-plane-arrival bg-blue"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fas fa-clock"></i> <?php echo tempo($resultado['abr_data_inicio']) ?></span>
                  <h3 class="timeline-header"> <b>Início do Abrigamento</b></h3>

                </div>
              </div>
              <!-- END timeline item -->
              
              
              <!-- timeline time label -->
              <?php $anterior = '';?>
              <?php while ($resultado2 = $consulta2->fetch_assoc()) { ?>
              <?php if($anterior != day($resultado2['aco_data'])){?>
              <div class="time-label">
                <span class="bg-blue"><?php echo dataEmPortugues(strtotime($resultado2['aco_data'])) ?></span>
              </div>
              <!-- timeline item -->
              <div>
                <i class="fas fa-list bg-yellow"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fas fa-clock"></i> <?php echo tempo($resultado2['aco_data']) ?></span>
                  <h3 class="timeline-header"><a href="#"><?php echo $resultado2['tec_apelido'] ?></a> adicionou um acompanhamento <?php if($resultado2['aco_tec_codigo']==$tecnico){?><a onclick="modal('<?php echo data($resultado2['aco_data']) ?>', <?php echo $resultado2['aco_codigo'] ?>)" data-toggle="modal" data-target="#modal-confirmar" href="#"><small>[excluir]</small></a><?php }?></h3>

                  <div class="timeline-body">
                    <?php echo $resultado2['aco_relatorio'] ?>
                  </div>
                </div>
              </div>
              <!-- END timeline item -->
              <?php $anterior = day($resultado2['aco_data']);?>
              <?php }else{ ?>
            <!-- timeline item -->
              <div>
                <i class="fas fa-list bg-yellow"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fas fa-clock"></i> <?php echo tempo($resultado2['aco_data']) ?></span>
                  <h3 class="timeline-header"><a href="#"><?php echo $resultado2['tec_apelido'] ?></a> adicionou um acompanhamento <?php if($resultado2['aco_tec_codigo']==$tecnico){?><a onclick="modal('<?php echo data($resultado2['aco_data']) ?>', <?php echo $resultado2['aco_codigo'] ?>)" data-toggle="modal" data-target="#modal-confirmar" href="#"><small>[excluir]</small></a><?php }?></h3>

                  <div class="timeline-body">
                    <?php echo $resultado2['aco_relatorio'] ?>
                  </div>
                </div>
              </div>
              <!-- END timeline item -->
              <?php } ?>
              <?php } ?>
            
              
              
              
              <?php if($resultado['abr_data_fim']!=''){ ?>
              <div class="time-label">
                <span class="bg-green"><?php echo dataEmPortugues(strtotime($resultado['abr_data_fim'])) ?></span>
              </div>
              <!-- /.timeline-label -->
              
              
              <!-- timeline item -->
              <div>
                <i class="fas fa-plane-departure bg-blue"></i>
                <div class="timeline-item">
                  <span class="time"><i class="fas fa-clock"></i> <?php echo tempo($resultado['abr_data_fim']) ?></span>
                  <h3 class="timeline-header"> <b>Fim do Abrigamento</b></h3>

                </div>
              </div>
              <?php } ?>
              <!-- END timeline item -->
              
              
              <div>
                <i class="fas fa-clock bg-gray"></i>
              </div>
            </div>
          </div>
          <!-- /.col -->
        </div>
        </div>
            </div>
            
            <?php if(@$msg==1) echo
                    "<div id='alerta' class='alert alert-danger' role='alert'>
                        Não é possível adicionar um acompanhamento vazio!
                    </div>";
            ?>
        <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Adicionar novo acompanhamento</h3>
              </div>
              
              <div class="card-body">
                  
              <div class="row">
          <div class="col-md-12">
            <!-- The time line -->
                        
                        <form role="form" method="POST" action="?" id="form">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Data e Hora do Acompanhamento</label>
                            <input name="data" type="datetime-local" required class="form-control" value="<?php echo date('Y-m-d') . 'T' . date('H:i')?>">
                        </div>
                        <p><b>Acompanhamento:</b></p>
                          <div class="mb-3">
                            <input type="hidden" value="<?php echo $codigo?>" name="abrigamento"> 
                            <textarea form="form" name="relatorio" class="textarea" style="width: 100%; height: 200px; font-size: 14px; 
                            line-height: 18px; border: 1px solid #dddddd; padding: 10px;" id="mytextarea"></textarea>
                          </div>
                          
                            
                        <div class="card-footer row justify-content-md-center justify-content-sm-center justify-content-center">
                          <button type="submit" class="btn btn-success">Adicionar</button>
                        </div>
                        </form>
          </div>
          <!-- /.col -->
        </div>
        </div>
            </div>
        
      </div><!-- /.container-fluid -->
    </section>
<div class="modal fade" id="modal-confirmar">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Confirmação</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form role="form" method="POST" action="?codigo=<?php echo $codigo?>">
            <div class="modal-body">
              <h5>Confirme se deseja excluir o acompanhamento abaixo:</h5>
              <div id="relatorio"></div>  <!-- recebe a escrita do relatorio -->
              <input type="password" name="password" class="form-control" id="password" placeholder="Informe sua senha para continuar">
              <input type="hidden" name="excluir" id="excluir">
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Confirmar</button>
            </div>
            </form>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>

<script>
    function modal(acompanhamento, excluir){ //preenche o modal de confirmar exclusao
        document.getElementById('relatorio').innerHTML = acompanhamento;
        document.getElementById('excluir').value = excluir;
    }
    
</script>

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