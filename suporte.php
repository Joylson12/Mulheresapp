<?php
include("config.php");
include("acesso.php");
include("permisaoAdm.php");

// Variáveis para definição antes de incluir o design1.php class="nav-link active"

$design_titulo = "Chamados de Suporte ao Sistema";
$design_ativo = "m11"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Suporte";
$design_migalha1_link = "";
$design_migalha2_texto = "";
$design_migalha2_link = "";

?>

<?php
    include("design1.php");
    if(isset($_GET['antigos'])){
        $consulta = $MySQLi->query("SELECT * from tb_chamados
                            join tb_tecnicos on cha_tec_codigo = tec_codigo
                            order by cha_data_pedido desc");
        $antigos = 1;
    }else{
        $consulta = $MySQLi->query("SELECT * from tb_chamados
                            join tb_tecnicos on cha_tec_codigo = tec_codigo
                            where cha_resposta is null or cha_data_pedido BETWEEN DATE_ADD(CURRENT_DATE(), INTERVAL -15 DAY) AND CURRENT_DATE()
                            order by cha_data_pedido desc");
    }
    if(isset($_POST['chamado'])){
            $chamado = $_POST['chamado'];
            if($chamado==''){
                header("Location: ?msg=1");  
            }else{ 
                $consulta = $MySQLi->query("INSERT INTO tb_chamados (cha_pedido, cha_tec_codigo) VALUES ('$chamado', $id)");
                header("Location: ?msg=2");
               
            }
    }
    
    if(isset($_GET['msg'])) $msg = $_GET['msg'];
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
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
          <?php if(@$msg==2) echo
                    "<div id='alerta' class='alert alert-success' role='alert'>
                        Chamado cadastrado com sucesso!
                    </div>";
            ?> 
        <div class="card card-primary card-outline">
          <div class="card-body">
        
        
        <blockquote>
            Este é o contato com a equipe técnica do sistema. Use este espaço para registrar problemas técnicos, falhas, recursos em falta, ou mesmo sugestões de recursos que ajudariam no seu dia a dia de uso do sistema. 
            Caso deseje relatar um erro, informe o máximo de detalhes que seja possível: em que página isto ocorreu, o que você fez e o que estava tentando fazer, para que a equipe possa reproduzir o mesmo erro nos testes.
        </blockquote>
        
        <div class="form-group">
                    <div class="custom-control custom-switch">
                      <input <?php if($antigos==1) echo 'checked' ?> type="checkbox" class="custom-control-input" id="customSwitch1" onclick="this.checked == true ? window.location.href = '?antigos=1' : window.location.href = '?'">
                      <label class="custom-control-label" for="customSwitch1">Exibir antigos</label>
                    </div>
                </div>
        
        <div class="row">
          <div class="col-12">
            <div class="card">
              
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered dataTable dtr-inline no-footer collapsed">
                  <thead>
                    <tr>
                      <th>Autor</th>
                      <th>Problema</th>
                      <th>Resposta técnica</th>
                    </tr>
                  </thead>
                  <tbody>
                      <?php while ($resultado = $consulta->fetch_assoc()) { ?>  
                      <tr class="text-justify">
                        <td><div class="text-center">
                                <div class="image">
                                  <img src="<?php echo 'imagens/tecnicos/' . $resultado['tec_codigo'] . '.jpg' ?>" class="img-circle elevation-2" width="30"><br>
                                </div>
                                <div class="info">
                                  <?php echo $resultado['tec_apelido']?>
                                </div>
                            </div>
                            
                            </td>
            			<td><?php echo data($resultado['cha_data_pedido']); ?> <br> <?php echo $resultado['cha_pedido']?></td>
            			<td><span class="text-green"><?php if($resultado['cha_data_resposta']!='') echo data($resultado['cha_data_resposta'])." <br> "; ?><?php echo $resultado['cha_resposta']?></td>
                    </tr>
                    <?php } ?>
                    
                  </tbody>
                </table>
                
                
                
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>

            
            
            
            
                <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Adicionar novo chamado de Suporte</h3>
              </div>
              
              <div class="card-body">
                  
            <!-- The time line -->
                        <?php if(@$msg==1) echo
                    "<div id='alerta' class='alert alert-danger' role='alert'>
                        Não é possível enviar um chamado vazio!
                    </div>";
            ?>
                        <form role="form" method="POST" action="?" id="form">
     
                        <p><b>Descreva o que deseja:</b></p>
                          <div class="mb-3">
                            <textarea form="form" name="chamado" class="textarea" style="width: 100%; height: 200px; font-size: 14px; 
                            line-height: 18px; border: 1px solid #dddddd; padding: 10px;" id="mytextarea"></textarea>
                          </div>
                          
        </div>
                            
                        <div class="card-footer text-center">
                          <button type="submit" class="btn btn-success">Cadastrar chamado</button>
                        </div>
                        </form>
      
          <!-- /.col -->
        </div>
            
            
            
            
            
			
			
          </div><!-- /.card-body -->
          
        </div>
      </div><!-- /.container-fluid -->
    </section>
    
    
    
    
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- page script -->
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>


<?php
include("design2.php");
?>