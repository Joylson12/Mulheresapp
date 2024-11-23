<?php
include("config.php");
include("acesso.php");
include("permisaoAdm.php");

// Variáveis para definição antes de incluir o design1.php class="nav-link active"

$design_titulo = "Abrigo";
$design_ativo = "m7"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Abrigo";
$design_migalha1_link = "";
$design_migalha2_texto = "";
$design_migalha2_link = "";

?>

<?php
    include("design1.php");
    if(isset($_GET['antigos'])){
        $consulta = $MySQLi->query("SELECT abr_codigo, mul_nome, mul_codigo, mul_foto, tec_apelido, abr_data_inicio, abr_data_fim, count(cab_pes_codigo) as dependentes FROM tb_abrigamentos
                            join tb_mulheres on abr_mul_codigo = mul_codigo
                            join tb_tecnicos on abr_tec_codigo = tec_codigo
                            left join tb_componentes_abrigamento on cab_abr_codigo = abr_codigo
                            group by abr_codigo
                            order by abr_data_inicio desc");
        $antigos = 1;
    }else{
        $consulta = $MySQLi->query("SELECT abr_codigo, mul_nome, mul_codigo, mul_foto, tec_apelido, abr_data_inicio, abr_data_fim, count(cab_pes_codigo) as dependentes FROM tb_abrigamentos
                            join tb_mulheres on abr_mul_codigo = mul_codigo
                            join tb_tecnicos on abr_tec_codigo = tec_codigo
                            left join tb_componentes_abrigamento on cab_abr_codigo = abr_codigo
                            where abr_data_fim is null or abr_data_fim > now()
                            group by abr_codigo
                            order by abr_data_inicio desc");
    }
    if(isset($_GET['msg'])) $msg=$_GET['msg'];
  
?>
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">




    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
          <?php if(@$msg==1) echo
                    "<div id='alerta' class='alert alert-success' role='alert'>
                        Abrigamento cadastrado com sucesso!
                    </div>";
            ?> 
            <?php if(@$msg==2) echo
                    "<div id='alerta' class='alert alert-success' role='alert'>
                        Abrigamento atualizado com sucesso!
                    </div>";
            ?> 
            
            
            
        <div class="card card-primary card-outline">
          
          <div class="card-header">
            <h3 class="card-title">Cadastro de Abrigadas</h3>
            </div>
          <div class="card-body">
              
    
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
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>Foto</th>
                      <th>Nome</th>
                      <th>Técnico</th>
                      <th>Data inicial</th>
                      <th>Data final </th>
                      <th>Dependentes</th>
                      <th>Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                      <?php while ($resultado = $consulta->fetch_assoc()) { ?> 
                      <tr>
                        <td><img class="img-circle elevation-2" width="30" src="<?php echo 'imagens/mulheres/' . $resultado['mul_foto'] . '.jpg'?>"></a></td>
            			<td><?php echo $resultado['mul_nome']?></td>
            			<td><?php echo $resultado['tec_apelido']?></td>
            			<td><?php echo hora($resultado['abr_data_inicio'])?></td>
            			<td><?php if($resultado['abr_data_fim']!='') echo hora($resultado['abr_data_fim']); else echo '-' ?></td>
            			<td><?php echo $resultado['dependentes']?></td>
                        <td>
                          <a href="/abrigamento-edit.php?codigo=<?php echo $resultado['abr_codigo']?>" ><button type="button" class="btn bg-gradient-primary btn-xs"><i class="nav-icon fas fa-pencil-alt"></i> Editar</button></a>
                          <a href="/abrigamento-acompanhar.php?codigo=<?php echo $resultado['abr_codigo']?>" ><button type="button" class="btn bg-gradient-primary btn-xs"><i class="nav-icon fas fa-list"></i> Acompanhamento</button></a>
                        </td>
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