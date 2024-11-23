<?php
include("config.php");
include("acesso.php");
// Variáveis para definição antes de incluir o design1.php class="nav-link active"

$design_titulo = "Equipe Técnica";
$design_ativo = "m4"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Equipe Técnica";
$design_migalha1_link = "";
$design_migalha2_texto = "";
$design_migalha2_link = "";

?>

<?php include("design1.php");
    $consulta = $MySQLi->query("SELECT tec_codigo, tec_apelido, tec_matricula, tec_telefone, tec_email, car_cargo, lot_lotacao FROM tb_tecnicos 
                            join tb_cargos on tec_car_codigo = car_codigo
                            join tb_lotacoes on tec_lot_codigo = lot_codigo
                            order by tec_apelido");
?>


    <section class="content">

      <!-- Default box -->
      <div class="card card-solid">
        <div class="card-body pb-0">
            
          <div class="row d-flex align-items-stretch">
           <?php while ($resultado = $consulta->fetch_assoc()) { ?>
            
            <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch">
              <div class="card bg-light">
                <div class="card-header text-muted border-bottom-0">
                  <?php echo $resultado['lot_lotacao'] ?>
                </div>
                <div class="card-body pt-0">
                  <div class="row">
                    <div class="col-7">
                      <h2 class="lead"><b><?php echo $resultado['tec_apelido'] ?></b></h2>
                      <p class="text-muted text-sm"><b>Profissão: </b> <?php echo $resultado['car_cargo'] ?> </p>
                      <p class="text-muted text-sm"><b>Matrícula: </b> <?php echo $resultado['tec_matricula'] ?> </p>
                      <ul class="ml-4 mb-0 fa-ul text-muted">
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> <?php echo $resultado['tec_telefone']; if($resultado['tec_telefone'] == "") echo " - "; ?></li>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-envelope"></i></span> <?php echo $resultado['tec_email']; if($resultado['tec_email']=="") echo " - "; ?></li>
                      </ul>
                    </div>
                    <div class="col-5 text-center">
                      <img src="<?php echo 'imagens/tecnicos/' . $resultado['tec_codigo'] . '.jpg' ?>" alt="" class="img-circle img-fluid">
                    </div>
                  </div>
                </div>
                <?php if($resultado['tec_codigo'] != $id){?>
                <div class="card-footer">
                  
                  <div class="text-right">
                                          <a href="mensagem-nova.php?tecnico=<?php echo $resultado['tec_codigo'] ?>" class="btn btn-sm bg-teal">
                      <i class="fas fa-envelope"></i> Enviar Mensagem
                    </a>
                   
                  </div>
                </div>
                <?php } ?>
              </div>
            </div>
            
            <?php } ?>
            
          </div>
        </div>
        
        
        <!-- /.card-footer -->
      </div>
      <!-- /.card -->

    </section>



<?php
include("design2.php");
?>