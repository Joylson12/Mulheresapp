<?php
include("config.php");
include("acesso.php");
// Variáveis para definição antes de incluir o design1.php class="nav-link active"

$design_titulo = "Mulheres";
$design_ativo = "m2"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Mulheres";
$design_migalha1_link = "";
$design_migalha2_texto = "";
$design_migalha2_link = "";

?>

<?php include("design1.php");
    if(isset($_GET['buscar'])) {
		$buscar = $_GET['buscar'];
		$consulta = $MySQLi->query("SELECT mul_codigo,mul_foto,mul_nome,mul_data_nasc,mul_cpf,mul_rg FROM tb_mulheres where soundex(mul_nome) like concat('%',soundex('$buscar'),'%') UNION 
		SELECT mul_codigo,mul_foto,mul_nome,mul_data_nasc,mul_cpf,mul_rg FROM tb_mulheres where mul_nome like '%$buscar%' limit 10");
	}else $consulta = $MySQLi->query("SELECT mul_codigo,mul_foto,mul_nome,mul_data_nasc,mul_cpf,mul_rg FROM tb_mulheres limit 10");
?>
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary card-outline">
          <div class="card-header">
            <h3 class="card-title">Cadastro de Mulheres</h3>
          </div> <!-- /.card-body -->
          <div class="card-body">
    
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title mb-sm-0 mb-2">Busque para filtrar resultados</h3>
                <div class="card-tools">
                <form action="?" method="get">
                  <div class="input-group input-group-sm" style="width: 150px;">
                    
                        <input type="text" name="buscar" class="form-control float-right" placeholder="Filtro por Nome">
    
                        <div class="input-group-append">
                          <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                        </div>
                    
                  </div>
                </form>
                </div>
                
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Foto</th>
                      <th>Nome</th>
                      <th>Nascimento</th>
                      <th>CPF</th>
                      <th>RG</th>
                      <!--<th>Movimentação</th>-->
                      <th>Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i=0;
                    while ($resultado = $consulta->fetch_assoc()) { $i++; ?>
                    <tr class="align-middle">
                        <td class="align-middle"><img class="img-circle elevation-2" alt="User Image" width="40" src="<?php echo 'imagens/mulheres/' . $resultado['mul_foto'] . '.jpg?' . time() ?>"></td>
            			<td class="align-middle"><?php echo $resultado['mul_nome']?></td>
            			<td class="align-middle"><?php if($resultado['mul_data_nasc']!='') echo data($resultado['mul_data_nasc']); else echo '-'?></td>
            			<td class="align-middle"><?php echo $resultado['mul_cpf']?></td>
            			<td class="align-middle"><?php echo $resultado['mul_rg']?></td>
                        <td class="align-middle"><a href="mulher-ver.php?codigo=<?php echo $resultado['mul_codigo']; ?>" class="btn btn-sm btn-primary btn-xs"><i class="fas fa-folder-open"></i> Ver</a></td>
                    </tr>
                    <?php } if($i==0) echo "<tr><td colspan='6'> Nenhuma mulher encontrada.</td></tr>"; ?>
                    
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
//   $(function () {
//     $("#example1").DataTable({
//       "responsive": true,
//       "autoWidth": false,
//     });
//     $('#example2').DataTable({
//       "paging": true,
//       "lengthChange": false,
//       "searching": false,
//       "ordering": true,
//       "info": true,
//       "autoWidth": false,
//       "responsive": true,
//     });
//   });
</script>

<?php
include("design2.php");
?>