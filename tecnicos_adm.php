<?php
include("config.php");
include("acesso.php");
include("permisaoAdm.php");

// Variáveis para definição antes de incluir o design1.php class="nav-link active"

$design_titulo = "Técnicos";
$design_ativo = "m4b"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Gerenciar Técnicos";
$design_migalha1_link = "";
$design_migalha2_texto = "";
$design_migalha2_link = "";

?>

<?php
    include("design1.php");
    $consulta = $MySQLi->query("SELECT tec_codigo, tec_apelido, tec_matricula, tec_email, tec_admin, tec_atende, lot_lotacao, car_cargo FROM tb_tecnicos 
                            join tb_cargos on tec_car_codigo = car_codigo
                            join tb_lotacoes on tec_lot_codigo = lot_codigo
                            order by tec_nome");
     if(isset($_GET['msg'])) $msg = $_GET['msg'];
?>
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
          <?php if(@$msg==1) echo
                    "<div id='alerta' class='alert alert-success' role='alert'>
                        Técnico cadastrado com sucesso!
                    </div>";
            ?>  
            <?php if(@$msg==2) echo
                    "<div id='alerta' class='alert alert-success' role='alert'>
                        Técnico atualizado com sucesso!
                    </div>";
                 ?> 
        <div class="card card-primary card-outline">
          <div class="card-body">
        
        <div class="row">
          <div class="col-12">
            <div class="card">
              
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>Foto</th>
                      <th>Nome</th>
                      <th>Matrícula</th>
                      <th>Email</th>
                      <th>Lotação</th>
                      <th>Cargo</th>
                      <th style="text-align: center;">Administrador</th>
                      <th style="text-align: center;">Atendimento</th>
                      <th>Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                      <?php while ($resultado = $consulta->fetch_assoc()) { ?>  
                      <tr>
                        <td><img class="img-circle elevation-2" width="30" src="<?php echo 'imagens/tecnicos/' . $resultado['tec_codigo'] . '.jpg'?>"></a></td>
            			<td><?php echo $resultado['tec_apelido']?></td>
            			<td><?php echo $resultado['tec_matricula']?></td>
            			<td><?php echo $resultado['tec_email']?></td>
            			<td><?php echo $resultado['lot_lotacao']?></td>
            			<td><?php echo $resultado['car_cargo']?></td>
            			<td style="text-align: center;"><?php if($resultado['tec_admin']==1) echo '<img src="imagens/yes.png" width="16">'; else echo '<img src="imagens/no.png" width="16">'?></td>
                        <td style="text-align: center;"><?php if($resultado['tec_atende']==1) echo '<img src="imagens/yes.png" width="16">'; else echo '<img src="imagens/no.png" width="16">'?></td>
                        <td>
                            <a href="tecnico-editar.php?codigo=<?php echo $resultado['tec_codigo']?>" class="btn btn-sm btn-primary btn-xs"><i class="fas fa-pencil-alt"></i> Editar</a>
                          <!--<a href="/tecnico-edit.php?codigo=<?php echo $resultado['tec_codigo']?>" ><i class="nav-icon fas fa-"></i></a>-->
                          </td>
                        </tr>
                    <?php } ?>
                    
                  </tbody>
                </table>
                
                
                <div class="card-footer">
                  <center><a href="tecnico-novo.php"><button type="button" class="btn btn-primary">Adicionar novo</button></a></center>
                </div>
                
                
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
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