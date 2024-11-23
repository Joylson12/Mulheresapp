<?php
include("config.php");
include("acesso.php");
// Variáveis para definição antes de incluir o design1.php class="nav-link active"

$design_titulo = "Mensagens";
$design_ativo = "m9"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Mensagens";
$design_migalha1_link = "";
$design_migalha2_texto = "";
$design_migalha2_link = "";

?>

<?php include("design1.php");
    // Caixas ($caixa):
    // 0 - Entrada (else)
    // 1 - Enviados
    // 2 - Arquivados
    // Status:
    // 0 - não lida
    // 1 - lida
    // 2 - Arquivada

    $nome = $_SESSION['nome'];
    if(isset($_GET['arquivar'])){
        $mensagem = $_GET['arquivar'];
        $caixa = $_GET['caixa'];
        $consulta = $MySQLi->query("SELECT men_tec_remetente, men_tec_destinatario FROM tb_mensagens
                                    WHERE men_codigo = $mensagem");
        $resultado = $consulta->fetch_assoc();
        if($resultado['men_tec_destinatario'] == $id){
            $consulta2 = $MySQLi->query("UPDATE tb_mensagens SET men_lida = 2 WHERE men_codigo = $mensagem and men_lida=1");
            header("Location: ?");
        }else $msg=2; 
    
    }
    if(isset($_POST['buscar'])) {
		$buscar = $_POST['buscar'];
		$caixa = $_GET['caixa'];
		if($caixa==0){
		    $consulta = $MySQLi->query("SELECT * FROM tb_mensagens 
                                JOIN tb_tecnicos on men_tec_remetente = tec_codigo
                                WHERE men_tec_destinatario = $id AND men_lida in(0, 1) AND
                                (men_titulo like '%$buscar%' or men_texto like '%$buscar%')
                                ORDER BY men_data DESC");
		}else if($caixa==1){
		    $consulta = $MySQLi->query("SELECT * FROM tb_mensagens 
                                JOIN tb_tecnicos on men_tec_destinatario = tec_codigo
                                WHERE men_tec_remetente = $id AND
                                (men_titulo like '%$buscar%' or men_texto like '%$buscar%')
                                ORDER BY men_data DESC");
		}else{
		    $consulta = $MySQLi->query("SELECT * FROM tb_mensagens 
                                JOIN tb_tecnicos on men_tec_remetente = tec_codigo
                                WHERE men_tec_destinatario = $id AND men_lida = 2 AND
                                (men_titulo like '%$buscar%' or men_texto like '%$buscar%')
                                ORDER BY men_data DESC");
		    
		}
		
	}else{
        if(isset($_GET['caixa'])){ //ver se o usuario que caixa de entrada ou enviados
            if($_GET['caixa']==1){
                $consulta = $MySQLi->query("SELECT * FROM tb_mensagens 
                                        JOIN tb_tecnicos on men_tec_destinatario = tec_codigo
                                        WHERE men_tec_remetente = $id
                                        ORDER BY men_data DESC");
                $caixa=1;
            }else if($_GET['caixa']==2){
                $consulta = $MySQLi->query("SELECT * FROM tb_mensagens 
                                    JOIN tb_tecnicos on men_tec_remetente = tec_codigo
                                    WHERE men_tec_destinatario = $id AND men_lida = 2
                                    and men_data BETWEEN DATE_ADD(CURRENT_DATE(), INTERVAL -60 DAY) AND CURRENT_DATE()
                                    ORDER BY men_data DESC");
                $caixa=2;
            }else header("Location: ?");
        }else{
            $consulta = $MySQLi->query("SELECT * FROM tb_mensagens 
                                    JOIN tb_tecnicos on men_tec_remetente = tec_codigo
                                    WHERE men_tec_destinatario = $id AND men_lida in(0, 1)
                                    ORDER BY men_data DESC");
            $caixa=0;
        }
	}
    
    $consulta3 = $MySQLi->query("UPDATE tb_mensagens SET men_lida = 1 WHERE men_tec_destinatario = $id and men_lida=0");
    
    if(isset($_GET['msg'])) $msg=$_GET['msg'];

?>


<section class="content">
      <div class="row">
        <div class="col-md-3">
          <a href="mensagem-nova.php" class="btn btn-primary btn-block mb-3">Nova mensagem</a>

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
                <li class="nav-item <?php if($caixa == 0) echo 'bg-yellow'; ?>">
                  <a href="?" class="nav-link">
                    <i class="fas fa-inbox"></i> Caixa de Entrada
                    <span class="badge bg-primary float-right"><?php echo $nolidas['nolidas'] ?></span>
                  </a>
                </li>
                <li class="nav-item <?php if($caixa == 1) echo 'bg-yellow'; ?>">
                  <a href="?caixa=1" class="nav-link">
                    <i class="far fa-envelope"></i> Enviados
                  </a>
                </li>
                <li class="nav-item <?php if($caixa == 2) echo 'bg-yellow'; ?>">
                  <a href="?caixa=2" class="nav-link">
                    <i class="far fa-trash-alt"></i> Arquivados
                  </a>
                </li>
                
              </ul>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
          
          <!-- /.card -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
            <?php if(@$msg==1) echo
                    "<div id='alerta' class='alert alert-success' role='alert'>
                        Mensagem arquivada com sucesso!
            </div>";?>
            <?php if(@$msg==2) echo
                    "<div id='alerta' class='alert alert-danger' role='alert'>
                        Você não pode arquivar essa mensagem!
            </div>";?>
            <?php if(@$msg==4) echo
                    "<div id='alerta' class='alert alert-danger' role='alert'>
                        Código de mensagem inválido!
            </div>";?>
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title"><?php if($caixa==0) echo 'Caixa de Entrada'; elseif($caixa==1) echo 'Enviados';else echo 'Arquivados (dos últimos 60 dias)'; ?></h3>

              <div class="card-tools">
                <form role="form" method="POST" action="?caixa=<?php echo $caixa ?>">
                <div class="input-group input-group-sm">
                  <input name="buscar" type="text" class="form-control" required placeholder="Buscar Mensagem">
                  <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                  </div>
                </div>
                </form>
              </div>
              <!-- /.card-tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
              <div class="table-responsive mailbox-messages">
                <table class="table table-hover table-striped">
                  <tbody>
                  <?php $i=0;
                  while ($resultado = $consulta->fetch_assoc()) { $i++; ?>
                  <tr>
                    <td>
                      <!--<div class="icheck-primary">-->
                        <!--<input type="checkbox" value="" id="check2">-->
                        <!--<label for="check2"></label>-->
                      <!--</div>-->
                      <?php if($resultado['men_lida']==0) { ?><i class="fas fa-asterisk text-yellow" title="Não lida"></i><?php } ?>
                    </td>
                    <td class="mailbox-name"><a href="mensagem-nova.php?tecnico=<?php echo $resultado['tec_codigo']?>"><?php echo $resultado['tec_apelido'] ?></a></td>
                    <td class="mailbox-subject"><b><?php echo $resultado['men_titulo'] ?></b> - <?php echo $resultado['men_texto'] ?></td>
                    <td class="mailbox-date"><?php if(day($resultado['men_data'])<date('Y-m-d')) echo dataEmPortugues(strtotime($resultado['men_data'])); else echo tempo($resultado['men_data'])?></td>
                    <?php if($caixa==0){ ?><td class="mailbox-date"><button type="button" onclick="window.location.href='mensagem-nova.php?mensagem=<?php echo $resultado['men_codigo'] ?>'"class="btn btn-default btn-sm"> <i class="fas fa-reply"></i></button>
                        <button type="button" onclick="window.location.href='?arquivar=<?php echo $resultado['men_codigo'] ?>'" class="btn btn-default btn-sm"><i class="far fa-trash-alt"></i></button></td>
                        <?php }?>
                  </tr>
                  <?php } if($i==0) echo "<tr><td colspan='5'>Nenhuma mensagem aqui!</td></tr>" ?>
                  
                  </tbody>
                </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer p-0">
              <div class="mailbox-controls">
                <!-- Check all button -->

                <!-- /.btn-group -->
                
                <!--<button type="button" class="btn btn-default btn-sm" onclick="window.location.href='?'"><i class="fas fa-sync-alt"></i></button>-->
                <div class="float-right"> &nbsp;
                <!--  1-50/200-->
                <!--  <div class="btn-group">-->
                <!--    <button type="button" class="btn btn-default btn-sm"><i class="fas fa-chevron-left"></i></button>-->
                <!--    <button type="button" class="btn btn-default btn-sm"><i class="fas fa-chevron-right"></i></button>-->
                <!--  </div>-->
                  <!-- /.btn-group -->
                </div>
                <!-- /.float-right -->
                
              </div>
            </div>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>


<?php
include("design2.php");
?>