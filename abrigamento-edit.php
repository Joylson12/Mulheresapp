<?php
include("config.php");
include("acesso.php");
include("permisaoAdm.php");
// Variáveis para definição antes de incluir o design1.php class="nav-link active"

$design_titulo = "Editar Abrigamento";
$design_ativo = "m7"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Abrigo";
$design_migalha1_link = "abrigo.php";
$design_migalha2_texto = "Editar Abrigamento";
$design_migalha2_link = "";

?>

<?php include("design1.php");
     if(isset($_GET['delPes'])){
        $pessoa = $_GET['delPes'];
        $abrigo = $_GET['delAbr'];
        $consulta6 = $MySQLi->query("DELETE FROM tb_componentes_abrigamento WHERE cab_abr_codigo = $abrigo AND cab_pes_codigo = $pessoa");
        header("Location: ?codigo=$abrigo&msg=1");
    }
    
    if(isset($_GET['msg'])) $msg=$_GET['msg'];
    
    if(isset($_GET['codigo'])){
        $cod = $_GET['codigo'];
        $consulta = $MySQLi->query("SELECT abr_codigo, abr_roldepertences, abr_mul_codigo, abr_tec_codigo, mul_nome, tec_apelido, abr_data_inicio, abr_data_fim, mul_codigo, tec_codigo FROM tb_abrigamentos 
                                JOIN tb_mulheres ON abr_mul_codigo = mul_codigo
                                JOIN tb_tecnicos ON abr_tec_codigo = tec_codigo
                                WHERE abr_codigo = $cod");
        $resultado = $consulta->fetch_assoc();
        $mulherCod = $resultado['mul_codigo'];
        $consulta4 = $MySQLi->query("SELECT pes_nome, pes_codigo, cab_abr_codigo, TIMESTAMPDIFF(YEAR, pes_data_nasc, NOW()) as idade FROM tb_componentes_abrigamento 
                                    JOIN tb_pessoas ON cab_pes_codigo = pes_codigo
                                    JOIN tb_mulheres ON pes_mul_codigo = mul_codigo
                                    WHERE cab_abr_codigo = $cod");
        $consulta5 = $MySQLi->query("SELECT pes_codigo, pes_nome FROM tb_pessoas 
                                    WHERE pes_codigo NOT IN (SELECT cab_pes_codigo FROM tb_componentes_abrigamento WHERE cab_abr_codigo = $cod) 
                                    AND pes_mul_codigo = $mulherCod");
    }else header("Location: abrigo.php");
    
    if(isset($_POST['pessoa'])){
        $pessoa = $_POST['pessoa'];
        $abrigo = $_POST['abrigo'];
        $consulta7 = $MySQLi->query("INSERT INTO tb_componentes_abrigamento (cab_abr_codigo, cab_pes_codigo) VALUES ($abrigo, $pessoa)");   
        header("Location: ?codigo=$abrigo&msg=2");
    }
    
    if(isset($_POST['codigo'])){
        $codigo = $_POST['codigo'];
        $mulher = $_POST['mulher'];
        $inicio = $_POST['inicio'];
        $fim = $_POST['fim'];
        $tecnico = $_POST['tecnico'];
        if($fim!=''){
            $consulta2 = $MySQLi->query("UPDATE tb_abrigamentos SET abr_mul_codigo = $mulher, abr_tec_codigo = $tecnico, abr_data_inicio = '$inicio', abr_data_fim = '$fim'
                                    WHERE abr_codigo = $codigo");
        }
        else{ 
            $consulta2 = $MySQLi->query("UPDATE tb_abrigamentos SET abr_mul_codigo = $mulher, abr_tec_codigo = $tecnico, abr_data_inicio = '$inicio', abr_data_fim = null
            WHERE abr_codigo = $codigo");
        }
        header("Location: abrigamento-edit.php?codigo=$codigo&msg=2");
        
    }
    $consulta3 = $MySQLi->query("SELECT tec_codigo, tec_apelido FROM tb_tecnicos WHERE tec_ativo = 1");
?>


    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Editar abrigamento</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
    
              <form role="form" action="?" method="POST">
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Nome</label>
                    <input type="hidden" name="codigo" value="<?php echo $resultado['abr_codigo'] ?>">
                    <input type="hidden" name="mulher" value="<?php echo $resultado['mul_codigo'] ?>">
                    <input type="text" class="form-control" value="<?php echo $resultado['mul_nome'] ?>" disabled>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Data inicial</label>
                        <input name="inicio" required type="datetime-local" class="form-control" value="<?php echo day($resultado['abr_data_inicio']) . 'T' .  tempo($resultado['abr_data_inicio'])?>">
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Data final</label>
                        <input name="fim" type="datetime-local" class="form-control" value="<?php if($resultado['abr_data_fim']!='') echo day($resultado['abr_data_fim']) . 'T' .  tempo($resultado['abr_data_fim'])?>">
                      </div>
                    </div>
                    
                  </div>
                  <div class="form-group">
                        <label for="exampleInputEmail1">Técnico</label>
                        <select name="tecnico" class="custom-select">
                                <?php while ($resultado3 = $consulta3 -> fetch_assoc()) {?>
                                <option value="<?php echo $resultado3['tec_codigo']; ?>" <?php if($resultado3['tec_codigo']==$resultado['tec_codigo']) echo 'selected' ?>><?php echo $resultado3['tec_apelido']; ?></option>
                                <?php } ?>
                        </select>
                    </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Pertences</label>
                    <textarea class="textarea" name="pertences" style="width: 100%; height: 200px; font-size: 14px;
                            line-height: 18px; border: 1px solid #dddddd; padding: 10px;" id="mytextarea" disabled><?php echo $resultado['abr_roldepertences'] ?></textarea>
                  </div>
                    
                </div>
                <div class="card-footer">
                  <center><button type="submit" class="btn btn-primary">Salvar</button></center>
                </div>
                <!-- /.card-body -->
              </form>
              
            </div>
              
              <div class="card card-secondary">
                      <div class="card-header">
                        <h3 class="card-title">
                          <i class="fas"></i>
                          Listagem de dependentes no Abrigamento
                        </h3>
                      </div>
                      <div class="card-body">
                         <?php if(@$msg==1) echo
                             "<div id='alerta' class='alert alert-success' role='alert'>
                                Dependente removido do abrigamento com sucesso!
                             </div>";
                        ?>
                        <?php if(@$msg==2) echo
                             "<div id='alerta' class='alert alert-success' role='alert'>
                                Dependente adicionado ao abrigamento com sucesso!
                             </div>";
                        ?>
                        <table class="table table-hover table-bordered" role="grid" aria-describedby="example1_info">
                          <thead>
                            <tr>
                              <th>Nome</th>
                              <th>Idade</th>
                              <th>Ação</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php while ($resultado4 = $consulta4->fetch_assoc()){?>
                            <tr>
                              <td><?php echo $resultado4['pes_nome'] ?></td>
                              <td><?php if($resultado4['idade']!='') echo $resultado4['idade'] . ' anos'; else echo '-' ?></td>
                              <td><button type="button" onclick="window.location.href='?delPes=<?php echo $resultado4['pes_codigo']?>&delAbr=<?php echo $resultado4['cab_abr_codigo']?>&codigo=<?php echo $resultado4['cab_abr_codigo']?>'" class="btn btn-block bg-gradient-primary btn-sm">Remover</button></td>
                            </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      
                           
            </div> 
                <div class="card-footer row justify-content-md-center justify-content-sm-center justify-content-center">
                    
                  <div class="form-group">
                      <form action="?" method="POST">
                            <label>Adicionar dependente (do cadastro social)</label>
                            <span class="input-group-append">
                            <input name="abrigo" type="hidden" value="<?php echo $cod ?>">
                            <select name="pessoa" class="custom-select">
                                <?php while ($resultado5 = $consulta5 -> fetch_assoc()) {?>
                                <option value="<?php echo $resultado5['pes_codigo']; ?>"><?php echo $resultado5['pes_nome']; ?></option>
                                <?php } ?>
                            </select>
                        <button type="submit" class="btn btn-success" data-toggle="modal" data-target="#modal-dependente">Adicionar</button></span>
                        </form>
                        </div>
                    
                </div>
                 
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