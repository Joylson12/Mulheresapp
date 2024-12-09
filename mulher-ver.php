<?php
include("config.php");
include("acesso.php");
include("arovim/funcoes.php");
include("includes/canvas.php");

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
  // Redireciona para a página de login ou exibe uma mensagem de erro
  header("Location: login.php");
  exit();
}

// Verificar se a chave is_admin está definida na sessão
$isAdmin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : false;


if (isset($_GET['codigo'])) {
  $codigo = $_GET['codigo'];  // Código da mulher
  $usuarioId = $_SESSION['id'];

  // Consultar o criador do atendimento
  $consultaCriador = $MySQLi->query("
      SELECT ate_tec_codigo1
      FROM tb_atendimentos
      WHERE ate_mul_codigo = $codigo
  ");
  $resultadoCriador = $consultaCriador->fetch_assoc();

  // Consultar técnicos autorizados com base no mul_codigo
  $consultaPermissao = $MySQLi->query("
      SELECT tec_codigo
      FROM tb_tecnicos_mulheres
      WHERE mul_codigo = $codigo  -- Relacionamento com a mulher
  ");

  // Definir a autorização: administrador tem permissão
  $isAuthorized = $isAdmin;

  // Verificar se o usuário é o criador ou um técnico autorizado
  if ($resultadoCriador['ate_tec_codigo1'] == $usuarioId) {
    $isAuthorized = true; // O usuário é o criador
  } else {
    // Verificar se o usuário é um dos técnicos autorizados
    while ($resultadoPermissao = $consultaPermissao->fetch_assoc()) {
      if ($resultadoPermissao['tec_codigo'] == $usuarioId) {
        $isAuthorized = true;
        break;  // Encontramos o técnico autorizado, não precisa continuar a busca
      }
    }
  }

  // Se não for autorizado, redireciona para página de erro
  if (!$isAuthorized) {
    header("Location: sem-permissao.php");
    exit();
  }

  // Registrar visualização, já que o usuário tem permissão
  $MySQLi->query("
      INSERT INTO tb_visualizacoes (vis_mul_codigo, vis_tec_codigo)
      VALUES ($codigo, $usuarioId)
  ");
}


// Variáveis para definição antes de incluir o design1.php class="nav-link active"

$design_titulo = "Cadastro da Mulher";
$design_ativo = "m2"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Mulheres";
$design_migalha1_link = "mulheres.php";
$design_migalha2_texto = "Ver Mulher";
$design_migalha2_link = "";

?>

<?php include("design1.php");
if (isset($_GET['msg']))
  $msg = $_GET['msg'];

if (isset($_FILES['foto']['name'])) {
  $mulher = $_POST['mulher'];
  $destino = 'imagens/mulheres/' . $mulher . ".jpg";
  $foto = $_FILES['foto']['tmp_name'];
  move_uploaded_file($foto, $destino);
  $imagem = new canvas();
  $imagem->carrega($destino);
  $imagem->redimensiona(300, 300, 'crop');
  $imagem->grava($destino, 88);
  $consulta4 = $MySQLi->query("UPDATE tb_mulheres SET mul_foto = $mulher where mul_codigo = $mulher");
  header("Location: ?codigo=" . $mulher);
}

if (isset($_GET['excluir'])) {
  $excluir = $_GET['excluir'];
  $mulher = $_GET['codigo'];
  if ($excluir == 1) {
    $consulta5 = $MySQLi->query("UPDATE tb_mulheres SET mul_foto = 0 where mul_codigo = $mulher");
    header("Location: ?codigo=" . $mulher);
  }
}

if (isset($_POST['pessoa'])) {
  $pessoa = $_POST['pessoa'];
  $mulher = $_POST['mulher'];
  $consulta6 = $MySQLi->query("INSERT INTO tb_pessoas (pes_nome, pes_mul_codigo) VALUES ('$pessoa', $mulher)");
  $id = mysqli_insert_id($MySQLi);
  header("Location: ?codigo=$mulher&pessoa=$id&msg=2");
}

if (isset($_POST['adversa'])) {
  $adversa = $_POST['adversa'];
  $mulher = $_POST['mulher'];
  $consulta9 = $MySQLi->query("INSERT INTO tb_agressores (agr_nome, agr_mul_codigo) VALUES ('$adversa', $mulher)");
  $id = mysqli_insert_id($MySQLi);
  header("Location: ?codigo=$mulher&adversa=$id&msg=3");
}
if (isset($_POST['atendimento'])) {
  $adversa = $_POST['adversa'];
  $mulher = $_GET['codigo'];
  $tecnico = $_SESSION['id'];
  $consulta11 = $MySQLi->query("INSERT INTO tb_atendimentos (ate_tec_codigo1,ate_mul_codigo,ate_resumo) VALUES ($tecnico,$mulher,'')");
  $id = mysqli_insert_id($MySQLi);
  header("Location: ?codigo=$mulher&atendimento=$id&msg=4");
}

// detalhes da pessoa, caso selecionada ou criada
if (isset($_GET['pessoa'])) {
  $pessoa = $_GET['pessoa'];
  $consulta7 = $MySQLi->query("SELECT *, TIMESTAMPDIFF(YEAR, pes_data_nasc, NOW()) as idade FROM tb_pessoas where pes_codigo = $pessoa");
  $resultado7 = $consulta7->fetch_assoc();
}
// detalhes da parte adversa, caso selecionada ou criada
if (isset($_GET['adversa'])) {
  $adversa = $_GET['adversa'];
  $consulta10 = $MySQLi->query("SELECT *,DATE_FORMAT(agr_oitiva_data, '%Y-%m-%dT%H:%i') AS agr_oitiva_data2 FROM tb_agressores where agr_codigo = $adversa");
  $resultado10 = $consulta10->fetch_assoc();
}
// detalhes do atendimento
if (isset($_GET['atendimento'])) {
  $atendimento = $_GET['atendimento'];
  $consulta12 = $MySQLi->query("SELECT *,DATE_FORMAT(ate_data, '%Y-%m-%dT%H:%i') AS ate_data2 FROM tb_atendimentos where ate_codigo = $atendimento");
  $resultado12 = $consulta12->fetch_assoc();
}


if (isset($_GET['codigo'])) {
  $codigo = $_GET['codigo'];
  $consulta = $MySQLi->query("SELECT *, a.alt_alternativa as bairro, b.alt_alternativa as cidade, TIMESTAMPDIFF(YEAR, mul_data_nasc, NOW()) as idade FROM tb_mulheres 
                                    left JOIN tb_alternativas a on mul_bairro = a.alt_codigo
                                    left JOIN tb_alternativas b on mul_cidade = b.alt_codigo
                                    left JOIN tb_tecnicos on mul_tec_codigo = tec_codigo
                                    where mul_codigo = $codigo");
  if (!$consulta)
    header("Location:mulheres.php");
  $resultado = $consulta->fetch_assoc();
  if ($resultado['mul_codigo'] == "")
    header("Location:mulheres.php");
  $consulta13 = $MySQLi->query("SELECT ate_data, ate_relatorio, a.tec_apelido as tec1, b.tec_apelido as tec2 FROM tb_atendimentos
                                    join tb_tecnicos a on ate_tec_codigo1 = a.tec_codigo
                                    join tb_tecnicos b on ate_tec_codigo2 = b.tec_codigo
                                    where ate_mul_codigo = $codigo
                                    order by ate_data desc
                                    limit 1");
  $resultado13 = $consulta13->fetch_assoc();
  $consulta14 = $MySQLi->query("SELECT * FROM vw_timeline_mulher
                                    where mulher = $codigo and data is not null
                                    order by data");

} else {
  header("Location:mulheres.php");
}

// análise para a tab padrão
if (isset($_GET['pessoa']))
  $aba = "pessoas";
elseif (isset($_GET['adversa']))
  $aba = "adversa";
elseif (isset($_GET['atendimento']))
  $aba = "atendimento";
else
  $aba = "timeline";

//listagem de pessoas
$consulta2 = $MySQLi->query("SELECT pes_codigo, pes_nome, alt_alternativa, TIMESTAMPDIFF(YEAR, pes_data_nasc, NOW()) as idade FROM tb_pessoas
        left join tb_alternativas on alt_codigo=pes_grau_parentesco where pes_mul_codigo = $codigo");
//listagem de agressores
$consulta3 = $MySQLi->query("SELECT agr_codigo, agr_nome, date_format(agr_data_cadastro,'%d/%m/%Y') as agr_data_cadastro FROM tb_agressores
        where agr_mul_codigo = $codigo");
//listagem de atendimentos
$consulta8 = $MySQLi->query("SELECT ate_codigo, date_format(ate_data,'%d/%m/%Y %H:%i') as data, a.tec_apelido as tec1, b.tec_apelido as tec2, tipo FROM tb_atendimentos
        join tb_tecnicos a on ate_tec_codigo1 = a.tec_codigo 
        left join tb_tecnicos b on ate_tec_codigo2 = b.tec_codigo
        left join vw_tipo_atendimento on ate_codigo = codigo
       where ate_mul_codigo = $codigo order by ate_codigo desc");

?>
<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/toastr/toastr.min.css">
<script src="https://cdn.tiny.cloud/1/k7vhbf0ybiy0bsqxhlfwwfww6zcohn8dz5eo1rg71vgdzsx3/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script src="arovim/arovim.js"></script>
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
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3">
        <?php if (@$msg == 1)
          echo
            "<div id='alerta' class='alert alert-success' role='alert'>
                        Mulher cadastrada com sucesso!
                    </div>";
        ?>
        <!-- Profile Image -->
        <div class="card card-primary card-outline">
          <div class="card-body box-profile">
            <div class="text-center">
              <img class="profile-user-img img-fluid img-circle"
                src="<?php echo 'imagens/mulheres/' . $resultado['mul_foto'] . '.jpg?' . time() ?>"
                title="Clique para alterar" data-toggle="modal" data-target="#modal-foto">
            </div>

            <h3 class="profile-username text-center"><?php echo $resultado['mul_nome'] ?></h3>

            <p class="text-muted text-center">
              <?php if ($resultado['cidade'] != '')
                echo $resultado['cidade'];
              if ($resultado['bairro'] != '')
                echo ' - ' . $resultado['bairro'] ?><br>
              <?php if ($resultado['idade'] != '')
                echo $resultado['idade'] . ' anos' ?>
              </p>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->

          <!-- About Me Box -->
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Resumo</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

              <strong><i class="fas fa-user mr-1"></i> Técnico de Referência</strong>

              <p class="text-muted"><?php echo $resultado['tec_nome'] ?></p>

            <hr>
            <strong><i class="fas fa-calendar mr-1"></i> Data Cadastro</strong>

            <p class="text-muted">
              <?php if ($resultado['mul_data_cadastro'] != '')
                echo data($resultado['mul_data_cadastro']); ?>
            </p>

            <hr>

            <strong><i class="fas fa-map-marker-alt mr-1"></i> Último Atendimento</strong>

            <p class="text-muted">
              <?php
              if (!empty($resultado13['ate_data']) && $resultado13['ate_data'] != '') {
                echo data($resultado13['ate_data']);
              } else {
                echo "Sem atendimentos registrados";
              }

              ?>
            </p>


          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
      <div class="col-md-9">
        <div class="card">
          <div class="card-header p-2">
            <ul class="nav nav-pills">
              <li class="nav-item"><a class="nav-link <?php if ($aba == "timeline")
                echo "active"; ?>" href="#timeline" data-toggle="tab" id="timeline-tab"
                  onclick="alternarAba('timeline')" class="btn btn-primary">Linha do Tempo</a></li>
              <li class="nav-item"><a class="nav-link <?php if ($aba == "cadastro")
                echo "active"; ?>" href="#cadastro" data-toggle="tab" id="cadastro-tab"
                  onclick="alternarAba('cadastro')" class="btn btn-secondary">Cadastro</a></li>
              <li class="nav-item"><a class="nav-link <?php if ($aba == "saude")
                echo "active"; ?>" href="#saude" data-toggle="tab" id="saude-tab" onclick="alternarAba('saude')"
                  class="btn btn-success">Saúde</a></li>
              <li class="nav-item"><a class="nav-link <?php if ($aba == "pessoas")
                echo "active"; ?>" href="#social" data-toggle="tab" id="social-tab" onclick="alternarAba('social')"
                  class="btn btn-success">Família</a></li>
              <li class="nav-item"><a class="nav-link <?php if ($aba == "adversa")
                echo "active"; ?>" href="#parteadversa" data-toggle="tab" id="parteadversa-tab"
                  onclick="alternarAba('parteadversa')" class="btn btn-success">Parte Adversa</a></li>
              <li class="nav-item"><a class="nav-link <?php if ($aba == "atendimento")
                echo "active"; ?>" href="#atendimento" data-toggle="tab" id="atendimento-tab"
                  onclick="alternarAba('atendimento')" class="btn btn-success">Atendimentos</a></li>
              <li class="nav-item"><a class="nav-link <?php if ($aba == "anexos")
                echo "active"; ?>" href="#anexos" data-toggle="tab" id="anexos-tab" onclick="alternarAba('anexos')"
                  class="btn btn-success">Anexos</a></li>
              <li class="nav-item"><a class="nav-link <?php if ($aba == "anexos")
                echo "active"; ?>" href="#permissao" data-toggle="tab" id="permissao-tab"
                  onclick="alternarAba('permissao')" class="btn btn-success">Permissão</a></li>
            </ul>
          </div><!-- /.card-header -->

          <script>
            // Função para alternar abas dinamicamente
            function alternarAba(aba) {
              // Remover a classe "active" de todas as abas
              document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));

              // Adicionar a classe "active" na aba selecionada
              document.getElementById(`${aba}-tab`).classList.add('active');

              // Exibir o conteúdo da aba correspondente
              document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
              document.getElementById(aba).classList.add('active');
            }
          </script>
          <div class="card-body">
            <div class="tab-content">
              <!-- /.tab-pane -->
              <div class="<?php if ($aba == "timeline")
                echo "active" ?> tab-pane" id="timeline">


                  <!-- The timeline -->
                  <div class="timeline timeline-inverse">
                  <?php $anterior = ''; ?>
                  <?php while ($resultado14 = $consulta14->fetch_assoc()) { ?>
                    <?php if ($anterior != mes($resultado14['data'])) { ?>
                      <!-- timeline time label -->
                      <div class="time-label">
                        <span class="bg bg-success">
                          <?php echo datamulher(strtotime($resultado14['data'])) ?>
                        </span>
                      </div>
                      <!-- /.timeline-label -->
                      <!-- timeline item -->
                      <?php if ($resultado14['tipo'] == 1) { ?>
                        <div>
                          <i class="fas fa-star bg-info"></i>

                          <div class="timeline-item">
                            <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                            <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                            </h3>
                          </div>
                        </div>
                      <?php } else if ($resultado14['tipo'] == 2) { ?>
                          <div>
                            <i class="fas fa-people-arrows bg-info"></i>

                            <div class="timeline-item">
                              <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                              <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                              <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                              </h3>
                            </div>
                          </div>
                      <?php } else if ($resultado14['tipo'] == 3) { ?>
                            <div>
                              <i class="fas fa-comments bg-warning text-white"></i>

                              <div class="timeline-item">
                                <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                <h3 class="timeline-header"><?php echo $resultado14['descricao'] ?></h3>

                                <div class="timeline-footer">
                                  <a href="?codigo=<?php echo $codigo ?>&atendimento=<?php echo $resultado14['codigo'] ?>"
                                    class="btn btn-warning btn-flat btn-sm">Ver relatório completo</a>
                                </div>
                              </div>
                            </div>
                      <?php } else if ($resultado14['tipo'] == 4) { ?>
                              <div>
                                <i class="fas fa-briefcase bg-info"></i>

                                <div class="timeline-item">
                                  <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                  <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                  <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                                  </h3>
                                </div>
                              </div>
                      <?php } else if ($resultado14['tipo'] == 5) { ?>
                                <div>
                                  <i class="fas fa-user-md bg-warning"></i>

                                  <div class="timeline-item">
                                    <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                    <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                    <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                                    </h3>
                                  </div>
                                </div>
                      <?php } else if ($resultado14['tipo'] == 6) { ?>
                                  <div>
                                    <i class="fas fa-brain bg-warning text-white"></i>

                                    <div class="timeline-item">
                                      <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                      <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                      <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                                      </h3>
                                    </div>
                                  </div>
                      <?php } else if ($resultado14['tipo'] == 7) { ?>
                                    <div>
                                      <i class="fas fa-capsules bg-secondary"></i>

                                      <div class="timeline-item">
                                        <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                        <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                        <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                                        </h3>
                                      </div>
                                    </div>
                      <?php } else if ($resultado14['tipo'] == 8) { ?>
                                      <div>
                                        <i class="fas fa-exclamation bg-danger"></i>

                                        <div class="timeline-item">
                                          <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                          <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                          <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                                          </h3>
                                        </div>
                                      </div>
                      <?php } else if ($resultado14['tipo'] == 9) { ?>
                                        <div>
                                          <i class="fas fa-shield-alt bg-success"></i>

                                          <div class="timeline-item">
                                            <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                            <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                            <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                                            </h3>
                                          </div>
                                        </div>
                      <?php } else if ($resultado14['tipo'] == 10) { ?>
                                          <div>
                                            <i class="fas fa-lock bg-success"></i>

                                            <div class="timeline-item">
                                              <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                              <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                              <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                                              </h3>
                                            </div>
                                          </div>
                      <?php } else if ($resultado14['tipo'] == 11) { ?>
                                            <div>
                                              <i class="fas fa-baby bg-info"></i>

                                              <div class="timeline-item">
                                                <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                                <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                                <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                                                </h3>
                                              </div>
                                            </div>
                      <?php } else if ($resultado14['tipo'] == 12) { ?>
                                              <div>
                                                <i class="fas fa-house-user bg-success"></i>

                                                <div class="timeline-item">
                                                  <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                                  <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                                  <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                                                  </h3>
                                                </div>
                                              </div>

                      <?php } ?>
                      <?php $anterior = mes($resultado14['data']); ?>
                    <?php } else { ?>
                      <?php if ($resultado14['tipo'] == 1) { ?>
                        <div>
                          <i class="fas fa-star bg-info"></i>

                          <div class="timeline-item">
                            <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                            <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                            </h3>
                          </div>
                        </div>
                      <?php } else if ($resultado14['tipo'] == 2) { ?>
                          <div>
                            <i class="fas fa-people-arrows bg-info"></i>

                            <div class="timeline-item">
                              <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                              <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                              <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                              </h3>
                            </div>
                          </div>
                      <?php } else if ($resultado14['tipo'] == 3) { ?>
                            <div>
                              <i class="fas fa-comments bg-warning"></i>

                              <div class="timeline-item">
                                <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                <h3 class="timeline-header"><?php echo $resultado14['descricao'] ?></h3>
                                <div class="timeline-footer">
                                  <a href="?codigo=<?php echo $codigo ?>&atendimento=<?php echo $resultado14['codigo'] ?>"
                                    class="btn btn-warning btn-flat btn-sm">Ver relatório completo</a>
                                </div>
                              </div>
                            </div>
                      <?php } else if ($resultado14['tipo'] == 4) { ?>
                              <div>
                                <i class="fas fa-briefcase bg-info"></i>

                                <div class="timeline-item">
                                  <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                  <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                  <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                                  </h3>
                                </div>
                              </div>
                      <?php } else if ($resultado14['tipo'] == 5) { ?>
                                <div>
                                  <i class="fas fa-user-md bg-warning"></i>

                                  <div class="timeline-item">
                                    <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                    <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                    <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                                    </h3>
                                  </div>
                                </div>
                      <?php } else if ($resultado14['tipo'] == 6) { ?>
                                  <div>
                                    <i class="fas fa-brain bg-warning"></i>

                                    <div class="timeline-item">
                                      <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                      <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                      <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                                      </h3>
                                    </div>
                                  </div>
                      <?php } else if ($resultado14['tipo'] == 7) { ?>
                                    <div>
                                      <i class="fas fa-capsules bg-secondary"></i>

                                      <div class="timeline-item">
                                        <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                        <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                        <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                                        </h3>
                                      </div>
                                    </div>
                      <?php } else if ($resultado14['tipo'] == 8) { ?>
                                      <div>
                                        <i class="fas fa-exclamation bg-danger"></i>

                                        <div class="timeline-item">
                                          <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                          <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                          <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                                          </h3>
                                        </div>
                                      </div>
                      <?php } else if ($resultado14['tipo'] == 9) { ?>
                                        <div>
                                          <i class="fas fa-shield-alt bg-success"></i>

                                          <div class="timeline-item">
                                            <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                            <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                            <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                                            </h3>
                                          </div>
                                        </div>
                      <?php } else if ($resultado14['tipo'] == 10) { ?>
                                          <div>
                                            <i class="fas fa-lock bg-success"></i>

                                            <div class="timeline-item">
                                              <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                              <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                              <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                                              </h3>
                                            </div>
                                          </div>
                      <?php } else if ($resultado14['tipo'] == 11) { ?>
                                            <div>
                                              <i class="fas fa-baby bg-info"></i>

                                              <div class="timeline-item">
                                                <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                                <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                                <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                                                </h3>
                                              </div>
                                            </div>
                      <?php } else if ($resultado14['tipo'] == 12) { ?>
                                              <div>
                                                <i class="fas fa-house-user bg-success"></i>

                                                <div class="timeline-item">
                                                  <span class="time"><i class="fas fa-calendar-alt"></i>
                              <?php echo data($resultado14['data']) ?></span>
                                                  <span class="time"><i class="fas fa-birthday-cake"></i> <?php echo $resultado14['idade'] ?></span>
                                                  <h3 class="timeline-header border-0"> <?php echo $resultado14['descricao'] ?>
                                                  </h3>
                                                </div>
                                              </div>

                      <?php } ?>


                    <?php } ?>
                  <?php } ?>
                  <!-- implementando logica para anexo -->
                  <?php
                  // Função para fazer upload de documentos
                  if (isset($_POST['upload']) && isset($codigo)) {
                    $arquivo = $_FILES['file'];
                    $mul_codigo = $codigo; // Utiliza o código já carregado na página
                  
                    // Verifica se o arquivo foi enviado sem erros
                    if ($arquivo['error'] === UPLOAD_ERR_OK) {
                      // Obtém o nome original do arquivo
                      $nomeArquivoOriginal = pathinfo($arquivo['name'], PATHINFO_FILENAME);
                      $extensaoArquivo = pathinfo($arquivo['name'], PATHINFO_EXTENSION);

                      // Verifica se foi fornecido um novo nome
                      $novoNome = trim($_POST['novo_nome']);
                      if (!empty($novoNome)) {
                        $nomeArquivo = $novoNome . '.' . $extensaoArquivo;
                      } else {
                        $nomeArquivo = $nomeArquivoOriginal . '.' . $extensaoArquivo;
                      }

                      $caminhoDestino = 'uploads/' . $nomeArquivo;

                      // Cria a pasta "uploads" se não existir
                      if (!is_dir('uploads')) {
                        mkdir('uploads', 0777, true);
                      }

                      // Move o arquivo para o diretório de destino
                      if (move_uploaded_file($arquivo['tmp_name'], $caminhoDestino)) {
                        // Insere o registro na tabela "documentos"
                        $consultaUpload = $MySQLi->prepare("INSERT INTO documentos (mulher_codigo, nome_arquivo, caminho_arquivo) VALUES (?, ?, ?)");
                        $consultaUpload->bind_param('iss', $mul_codigo, $nomeArquivo, $caminhoDestino);
                        $consultaUpload->execute();

                        if ($consultaUpload->affected_rows > 0) {
                          echo "<script>alert('Documento carregado com sucesso!');</script>";
                        } else {
                          echo "<script>alert('Erro ao salvar o documento.');</script>";
                        }

                        $consultaUpload->close();
                      } else {
                        echo "<script>alert('Erro ao mover o arquivo.');</script>";
                      }
                    } else {
                      echo "<script>alert('Erro no upload do arquivo.');</script>";
                    }
                  }

                  // Função para buscar documentos relacionados à mulher
                  function buscarDocumentos($MySQLi, $codigo)
                  {
                    $consultaDocs = $MySQLi->prepare("SELECT id, nome_arquivo, caminho_arquivo, data_upload FROM documentos WHERE mulher_codigo = ?");
                    $consultaDocs->bind_param('i', $codigo);
                    $consultaDocs->execute();
                    return $consultaDocs->get_result();
                  }
                  ?>
                  <!-- END timeline item -->



                  <div>
                    <i class="far fa-clock bg-gray"></i>
                  </div>
                </div>
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="cadastro">

                <!--Aqui inicia o cadastro de Mulher.-->
                <!---->
                <!---->
                <!---->
                <!---->
                <!---->
                <!---->


                <div class="card card-danger card-outline">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas"></i>
                      Prontuário
                    </h3>
                  </div>
                  <div class="card-body">

                    <div class="row">
                      <div class="col-sm-4">


                        <div class="form-group">
                          <label for="mul_prontuario">Nº Prontuário Físico</label>
                          <input name="numeroprontuario" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_prontuario')"
                            value="<?php echo $resultado['mul_prontuario']; ?>">
                        </div>
                      </div>
                      <div class="col-sm-4">

                        <?php
                        echo geraselect(1, $codigo, 'mul_encaminhada', 'Encaminhada de');
                        ?>

                        <div data-visibility-target="mul_encaminhada" data-visibility-value="8">
                          <div class="form-group">
                            <input name="numeroprontuario" type="text" class="form-control"
                              onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_encaminhada_outros')"
                              value="<?php echo $resultado['mul_encaminhada_outros']; ?>" placeholder="Outro">
                          </div>
                        </div>

                        <!--<input type="checkbox" name="usadrogas" id="usadrogas" value="sim"> usa drogas?-->
                        <!--<div data-visibility-target="usadrogas" data-visibility-value="sim">qual droga?</div>-->

                        <?php // esta aqui é uma consulta para outra tabela, devvb  cc tecnicos ?>
                      </div>
                      <div class="col-sm-4">

                        <div class="form-group">
                          <label>Profissional de referência</label>
                          <select name="referencia" class="custom-select"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_tec_codigo')">
                            <option> </option>
                            <?php $consultaprof = $MySQLi->query("SELECT * FROM tb_tecnicos where tec_ativo = 1");
                            while ($resultadoprof = $consultaprof->fetch_assoc()) { ?>
                              <option value="<?php echo $resultadoprof['tec_codigo']; ?>" <?php

                                 if ($resultadoprof['tec_codigo'] == $resultado['mul_tec_codigo'])
                                   echo "selected='selected'";

                                 ?>><?php echo $resultadoprof['tec_nome']; ?></option>
                            <?php } ?>
                          </select>
                        </div>

                      </div>
                    </div>

                  </div>
                </div>

                <div class="card card-danger card-outline">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas"></i>
                      Identificação da Usuária
                    </h3>
                  </div>
                  <div class="card-body">

                    <div class="row">
                      <div class="col-sm-4">

                        <div class="form-group">
                          <label for="mul_nome">Nome</label>
                          <input name="nome" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_nome')"
                            value="<?php echo $resultado['mul_nome']; ?>">
                        </div>


                      </div>
                      <div class="col-sm-4">


                        <div class="form-group">
                          <label for="mul_data_nasc">Data de Nascimento</label>
                          <input name="datanascimento" type="date" class="form-control"
                            onblur="submetevalor(this.value, <?php echo $codigo ?>, 'mul_data_nasc')"
                            value="<?php echo $resultado['mul_data_nasc']; ?>">
                        </div>


                      </div>
                      <div class="col-sm-4">



                        <div class="form-group">
                          <label for="mul_naturalidade">Naturalidade/UF</label>
                          <input name="naturalidade" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_naturalidade')"
                            value="<?php echo $resultado['mul_naturalidade']; ?>">
                        </div>

                      </div>
                    </div>

                    <div class="row">
                      <div class="col-sm-4">

                        <div class="form-group">
                          <label for="mul_rg">RG/Orgão</label>
                          <input name="rg" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_rg')"
                            value="<?php echo $resultado['mul_rg']; ?>">
                        </div>


                      </div>
                      <div class="col-sm-4">


                        <div class="form-group">
                          <label for="mul_cpf">CPF</label>
                          <input name="cpf" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_cpf')"
                            value="<?php echo $resultado['mul_cpf']; ?>">
                        </div>



                      </div>
                      <div class="col-sm-4">


                        <?php echo geraselect(2, $codigo, 'mul_religiao', 'Religião'); ?>

                      </div>
                    </div>



                    <div class="row">
                      <div class="col-sm-4">


                        <?php echo geraselect(3, $codigo, 'mul_cor', 'Cor'); ?>


                      </div>
                      <div class="col-sm-4">


                        <?php echo geraselect(4, $codigo, 'mul_estado_civil', 'Estado Civil'); ?>


                      </div>
                      <div class="col-sm-4">

                        <div class="form-group">
                          <label for="mul_estado_civil_data">Estado Civil desde (data)</label>
                          <input name="estadocivil" type="date" class="form-control"
                            onblur="submetevalor(this.value, <?php echo $codigo ?>, 'mul_estado_civil_data')"
                            value="<?php echo $resultado['mul_estado_civil_data']; ?>">
                        </div>

                      </div>
                    </div>


                    <div class="row">
                      <div class="col-sm-3">


                        <?php echo geraselect(5, $codigo, 'mul_identidade_genero', 'Identidade de Gênero'); ?>


                      </div>
                      <div class="col-sm-3">

                        <div class="form-group" data-visibility-target="mul_identidade_genero"
                          data-visibility-value="91">
                          <label for="mul_identidade_genero_outros">I. de gênero (outros)</label>
                          <input name="identidadegenero" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_identidade_genero_outros')"
                            value="<?php echo $resultado['mul_identidade_genero_outros']; ?>">
                        </div>


                      </div>
                      <div class="col-sm-3">


                        <?php echo geraselect(6, $codigo, 'mul_orientacao_sexual', 'Orientação Sexual'); ?>


                      </div>
                      <div class="col-sm-3">

                        <div class="form-group" data-visibility-target="mul_orientacao_sexual"
                          data-visibility-value="289">
                          <label for="mul_orientacao_sexual_outros">O. Sexual (outros)</label>
                          <input name="orientacaosexual" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_orientacao_sexual_outros')"
                            value="<?php echo $resultado['mul_orientacao_sexual_outros']; ?>">
                        </div>


                      </div>
                    </div>


                  </div>
                </div>



                <div class="card card-danger card-outline">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas"></i>
                      Domicílio
                    </h3>
                  </div>
                  <div class="card-body">


                    <div class="row">
                      <div class="col-sm-4">

                        <div class="form-group">
                          <label for="mul_endereco">Endereço</label>
                          <input name="endereco" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_endereco')"
                            value="<?php echo $resultado['mul_endereco']; ?>" id='enderecomulher'>
                        </div>


                      </div>
                      <div class="col-sm-4">


                        <?php echo geraselect(7, $codigo, 'mul_bairro', 'Bairro'); ?>


                      </div>
                      <div class="col-sm-4">


                        <?php echo geraselect(8, $codigo, 'mul_cidade', 'Cidade'); ?>

                      </div>
                    </div>


                    <div class="row">
                      <div class="col-sm-4">
                        <div class="form-group">
                          <label for="mul_referencia">Referência</label>
                          <input name="referencia" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_referencia')"
                            value="<?php echo $resultado['mul_referencia']; ?>" id='referenciamulher'>
                        </div>



                      </div>
                      <div class="col-sm-4">

                        <div class="form-group">
                          <label for="mul_telefone1">Telefone 1</label>
                          <input name="telefone1" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_telefone1')"
                            value="<?php echo $resultado['mul_telefone1']; ?>">
                        </div>


                      </div>
                      <div class="col-sm-4">

                        <div class="form-group">
                          <label for="mul_telefone2">Telefone 2</label>
                          <input name="telefone2" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_telefone2')"
                            value="<?php echo $resultado['mul_telefone2']; ?>">
                        </div>

                      </div>
                    </div>


                    <div class="row">
                      <div class="col-sm-4">


                        <div class="form-group">
                          <label for="mul_email">E-mail</label>
                          <input name="email" type="email" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_email')"
                            value="<?php echo $resultado['mul_email']; ?>">
                        </div>


                      </div>
                      <div class="col-sm-4">


                        <?php echo geraselect(9, $codigo, 'mul_residencia', 'Posse da Residência'); ?>


                      </div>
                      <div class="col-sm-4">


                        <?php echo geraselect(10, $codigo, 'mul_tipo_residencia', 'Tipo da Residência'); ?>

                      </div>
                    </div>

                  </div>
                </div>


                <div class="card card-danger card-outline">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas"></i>
                      Educação
                    </h3>
                  </div>
                  <div class="card-body">


                    <div class="row">
                      <div class="col-sm-4">


                        <?php echo geraselect(11, $codigo, 'mul_grau_instrucao', 'Grau de Instrução'); ?>



                        <div class="checkbox">
                          <label>
                            <input name="estudanto" id="estudandoatualmente" type="checkbox"
                              onchange="submetetruefalse(this.checked, <?php echo $codigo ?>, 'mul_estudando')" <?php if ($resultado['mul_estudando'] == '1')
                                   echo "checked='checked'"; ?> value="sim"> Estudando
                            atualmente</a>
                          </label>
                        </div>


                      </div>
                      <div class="col-sm-8">

                        <div class="form-group" data-visibility-target="mul_grau_instrucao"
                          data-visibility-value="117,118,119,120">
                          <label for="mul_curso_atual">Qual ano/curso/especialização?</label>
                          <input name="curso" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_curso_atual')"
                            value="<?php echo $resultado['mul_curso_atual']; ?>">
                        </div>

                      </div>
                    </div>


                    <div class="row">
                      <div class="col-sm-4">
                        <!--<div style="display:block"><input type="checkbox" id="inverso_estudando" value="sim" /></div>-->
                        <!--<div data-visibility-target="inverso_estudando" data-visibility-value="sim"></div>-->
                        <div class="form-group">
                          <label for="mul_ate_que_serie">Se não, Estudou até que série?</label>
                          <input name="seriefinal" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_ate_que_serie')"
                            value="<?php echo $resultado['mul_ate_que_serie']; ?>">
                        </div>


                      </div>
                      <div class="col-sm-8">

                        <div class="form-group">
                          <label for="mul_motivo_evasao">Motivo da evasão</label>
                          <input name="motivo" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_motivo_evasao')"
                            value="<?php echo $resultado['mul_motivo_evasao']; ?>">
                        </div>

                      </div>
                    </div>



                    <div class="row">
                      <div class="col-sm-8">

                        <?php echo geracheckbox(13, $codigo, "Cursos que já fez", "|"); ?>


                      </div>
                      <div class="col-sm-4">

                        <div class="form-group">
                          <label for="mul_cursos_realizados_outros">Outros cursos que já fez</label>
                          <input name="ourtoscursos" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_cursos_realizados_outros')"
                            value="<?php echo $resultado['mul_cursos_realizados_outros']; ?>">
                        </div>


                      </div>
                    </div>

                    <div class="row">
                      <div class="col-sm-8">


                        <?php echo geracheckbox(12, $codigo, "Cursos que tem interesse", "|"); ?>


                      </div>
                      <div class="col-sm-4">

                        <div class="form-group">
                          <label for="mul_cursos_interesse_outros">Outros cursos que tenho interesse</label>
                          <input name="cursosinteresse" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_cursos_interesse_outros')"
                            value="<?php echo $resultado['mul_cursos_interesse_outros']; ?>">
                        </div>


                      </div>
                    </div>


                  </div>
                </div>


                <div class="card card-danger card-outline">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas"></i>
                      Situação Trabalhista/previdenciária
                    </h3>
                  </div>
                  <div class="card-body">


                    <div class="row">
                      <div class="col-sm-4">

                        <div class="form-group">
                          <label for="mul_profissao">Profissão</label>
                          <input name="profissao" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_profissao')"
                            value="<?php echo $resultado['mul_profissao']; ?>">
                        </div>


                      </div>
                      <div class="col-sm-4">

                        <div class="form-group">
                          <label for="mul_experiencia_profissional">Experiência profissional</label>
                          <input name="experiencia" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_experiencia_profissional')"
                            value="<?php echo $resultado['mul_experiencia_profissional']; ?>">
                        </div>


                      </div>
                      <div class="col-sm-4">


                        <?php echo geraselect(14, $codigo, 'mul_ocupacao', 'Ocupação'); ?>

                      </div>
                    </div>


                    <div class="row">
                      <div class="col-sm-4">

                        <div class="form-group" data-visibility-target="mul_ocupacao"
                          data-visibility-value="131,134,135">
                          <label for="mul_empresa">Empresa onde trabalha</label>
                          <input name="empresa" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_empresa')"
                            value="<?php echo $resultado['mul_empresa']; ?>">
                        </div>


                      </div>
                      <div class="col-sm-4">

                        <div class="form-group" data-visibility-target="mul_ocupacao"
                          data-visibility-value="131,134,135">
                          <label for="mul_telefone_empresa">Telefone Empresa</label>
                          <input name="telefoneempresa" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_telefone_empresa')"
                            value="<?php echo $resultado['mul_telefone_empresa']; ?>">
                        </div>


                      </div>
                      <div class="col-sm-4">


                        <div class="form-group" data-visibility-target="mul_ocupacao"
                          data-visibility-value="132,135,136,137">
                          <label for="mul_data_desempregada">Desempregada desde</label>
                          <input name="desempregada" type="date" class="form-control"
                            onblur="submetevalor(this.value, <?php echo $codigo ?>, 'mul_data_desempregada')"
                            value="<?php echo $resultado['mul_data_desempregada']; ?>">
                        </div>

                      </div>
                    </div>


                    <div class="row">
                      <div class="col-sm-6">

                        <div class="checkbox">
                          <label>
                            <input name="aviso" type="checkbox"
                              onchange="submetetruefalse(this.checked, <?php echo $codigo ?>, 'mul_aviso_empresa_por_abrigamento')"
                              <?php if ($resultado['mul_aviso_empresa_por_abrigamento'] == '1')
                                echo "checked='checked'"; ?>> Aviso imediato ao Local/Empresa, em caso de abrigamento?</a>
                          </label>
                        </div>


                      </div>
                      <div class="col-sm-6">

                        <div class="form-group">
                          <label for="mul_aviso_empresa_motivo">Se não, porque?</label>
                          <input name="motivoaviso" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_aviso_empresa_motivo')"
                            value="<?php echo $resultado['mul_aviso_empresa_motivo']; ?>">
                        </div>

                      </div>
                    </div>


                    <div class="row">
                      <div class="col-sm-6">


                        <?php echo geraselect(16, $codigo, 'mul_faixa_salarial', 'Faixa Salarial'); ?>

                      </div>
                      <div class="col-sm-6">

                        <div class="form-group">
                          <label for="mul_nis">Número de Identificação Social (NIS)</label>
                          <input name="nis" type="text" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_nis')"
                            value="<?php echo $resultado['mul_nis']; ?>">
                        </div>

                      </div>
                    </div>


                    <div class="row">
                      <div class="col-sm-6">

                        <?php echo geracheckbox(17, $codigo, "Benefícios que Recebe", "|"); ?>


                      </div>
                      <div class="col-sm-6">

                        <div class="form-group">
                          <label for="mul_total_beneficios">Valor total dos benefícios</label>
                          <input name="valortotal" type="number" class="form-control"
                            onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_total_beneficios')"
                            value="<?php echo $resultado['mul_total_beneficios']; ?>">
                        </div>


                      </div>
                    </div>

                  </div>
                </div>


                <div class="card card-danger card-outline">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas"></i>
                      Serviço Social
                    </h3>
                  </div>
                  <div class="card-body">

                    Marque os serviços utilizados


                    <div class="row">
                      <div class="col-sm-6">


                        <?php echo geracheckbox(19, $codigo, "Proteção Social Básica", "<br>"); ?>

                      </div>
                      <div class="col-sm-6">


                        <?php echo geracheckbox(20, $codigo, "Proteção Social Especial de Média Complexidade", "<br>"); ?>

                      </div>
                    </div>

                  </div>
                </div>



                <div class="card card-danger card-outline">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas"></i>
                      Violência Doméstica e Familiar
                    </h3>
                  </div>
                  <div class="card-body">



                    <?php echo geracheckbox(21, $codigo, "Já sofreu ou sofre algum tipo de violência?", "<br>"); ?>
                    <br>

                    <?php echo geracheckbox(22, $codigo, "Que tipo de violência sofre atualmente?", "<br>"); ?>
                    <br>

                    <?php echo geracheckbox(23, $codigo, "Em que local ocorreu à violência?", "<br>"); ?>
                    <br>


                    <div class="checkbox">
                      <label>
                        <input id="abrigada" value="sim" type="checkbox"
                          onchange="submetetruefalse(this.checked, <?php echo $codigo ?>, 'mul_ja_abrigada')" <?php if ($resultado['mul_ja_abrigada'] == '1')
                               echo "checked='checked'"; ?>> Já foi abrigada na
                        CACC?</a>
                      </label>
                    </div>

                    <div class="form-group" data-visibility-target="abrigada" data-visibility-value="sim">
                      <label for="mul_abrigada_vezes">Quantas vezes foi abrigada?</label>
                      <input name="quantasvezesabrigada" type="number" class="form-control"
                        onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_abrigada_vezes')"
                        value="<?php echo $resultado['mul_abrigada_vezes']; ?>">
                    </div>

                    <div class="form-group">
                      <label for="mul_local_seguro">Estando em situação de risco iminente de morte tem algum local que
                        considera seguro para além da CACC? Se sim, qual é o local? Informe Nome e contato telefônico de
                        alguém que resida no local indicado</label>
                      <input name="lugaralemdocacc" type="text" class="form-control"
                        onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_local_seguro')"
                        value="<?php echo $resultado['mul_local_seguro']; ?>">
                    </div>

                  </div>
                </div>


                <!--<div class="card-footer row justify-content-md-center justify-content-sm-center justify-content-center">-->
                <!--  <button onclick2="return verifica()" type="button" class="btn btn-primary">Salvar Alterações</button>-->
                <!--</div>-->

                <!--Fim do cadastro de mulher-->
                <!---->
                <!---->
                <!---->
                <!---->
                <!---->
                <!---->


              </div>

              <div class="<?php if ($aba == "adversa")
                echo "active" ?> tab-pane" id="parteadversa">
                <?php if (@$msg == 3)
                echo
                  "<div id='alerta' class='alert alert-success' role='alert'>
                            Parte adversa adicionada!
                        </div>";
              ?>

                <div class="card card-secondary">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas"></i>
                      Listagem de partes adversas
                    </h3>
                  </div>
                  <div class="card-body">

                    <table class="table table-hover table-bordered" role="grid" aria-describedby="example1_info">
                      <thead>
                        <tr>
                          <th>Nome</th>
                          <th>Data cadastro</th>
                          <th>Ação</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php while ($resultado3 = $consulta3->fetch_assoc()) { ?>
                          <tr>
                            <td class="align-middle"><?php echo $resultado3['agr_nome']; ?></td>
                            <td class="align-middle"><?php echo $resultado3['agr_data_cadastro']; ?></td>
                            <td class="align-middle"><a
                                href="?codigo=<?php echo $codigo; ?>&adversa=<?php echo $resultado3['agr_codigo']; ?>"
                                ?<button type="button" class="btn btn-block bg-gradient-primary btn-xs"><i
                                  class="fas fa-search"></i> ver</button></a></td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>


                  </div>
                  <div class="card-footer justify-content-md-center text-center">
                    <button type="submit" class="btn btn-success" data-toggle="modal"
                      data-target="#modal-adversa">Adicionar nova parte adversa</button>
                  </div>

                </div>

                <?php if (isset($_GET['adversa'])) { ?>
                  <div class="card card-danger card-outline">
                    <div class="card-header">
                      <h3 class="card-title">
                        <i class="fas"></i>
                        Detalhes - Parte Adversa
                      </h3>
                    </div>
                    <div class="card-body">

                      <form class="form-horizontal">


                        <div class="row">
                          <div class="col-sm-4">

                            <div class="form-group">
                              <label for="agr_nome">Nome</label>
                              <input name="nome" type="text" class="form-control"
                                onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_nome')"
                                value="<?php echo $resultado10['agr_nome']; ?>">
                            </div>

                          </div>
                          <div class="col-sm-4">

                            <div class="form-group">
                              <label for="agr_apelido">Apelido</label>
                              <input name="apelido" type="text" class="form-control"
                                onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_apelido')"
                                value="<?php echo $resultado10['agr_apelido']; ?>">
                            </div>

                          </div>
                          <div class="col-sm-4">

                            <div class="form-group">
                              <label for="agr_data_nasc">Data de Nascimento</label>
                              <input name="datanascimento" type="date" class="form-control"
                                onblur="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_data_nasc')"
                                value="<?php echo $resultado10['agr_data_nasc']; ?>">
                            </div>


                          </div>
                        </div>


                        <div class="row">
                          <div class="col-sm-4">

                            <?php echo geraselectadversa(5, $codigo, 'agr_sexo', 'Sexo'); ?>


                          </div>
                          <div class="col-sm-4">

                            <div class="form-group">
                              <label for="agr_naturalidade">Naturalidade/UF</label>
                              <input name="naturalidade" type="text" class="form-control"
                                onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_naturalidade')"
                                value="<?php echo $resultado10['agr_naturalidade']; ?>">
                            </div>


                          </div>
                          <div class="col-sm-4">

                            <?php echo geraselectadversa(3, $codigo, 'agr_cor', 'Cor'); ?>

                          </div>
                        </div>

                        <div class="row">
                          <div class="col-sm-8">

                            <script>
                              function copiaendereco() {
                                document.getElementById('enderecoagressor').value = document.getElementById('enderecomulher').value;
                                document.getElementById('bairroagressor').value = document.getElementById('mul_bairro').options[document.getElementById('mul_bairro').selectedIndex].text;
                                document.getElementById('referenciaagressor').value = document.getElementById('referenciamulher').value;
                                document.getElementById('cidadeagressor').value = document.getElementById('mul_cidade').options[document.getElementById('mul_cidade').selectedIndex].text;
                                submetevaloradversa(document.getElementById('enderecoagressor').value, <?php echo $adversa ?>, 'agr_endereco');
                                submetevaloradversa(document.getElementById('bairroagressor').value, <?php echo $adversa ?>, 'agr_bairro');
                                submetevaloradversa(document.getElementById('referenciaagressor').value, <?php echo $adversa ?>, 'agr_referencia');
                                submetevaloradversa(document.getElementById('cidadeagressor').value, <?php echo $adversa ?>, 'agr_cidade');
                              }
                            </script>

                            <div class="form-group">
                              <label for="agr_endereco">Endereço <button type="button"
                                  class="btn bg-gradient-secondary btn-xs" onclick="copiaendereco()">copiar do endereço da
                                  mulher</button></label>
                              <input name="enderecoagressor" type="text" class="form-control"
                                onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_endereco')"
                                value="<?php echo $resultado10['agr_endereco']; ?>" id='enderecoagressor'>
                            </div>

                          </div>
                          <div class="col-sm-4">

                            <div class="form-group">
                              <label for="agr_bairro">Bairro</label>
                              <input name="bairroagressor" type="text" class="form-control"
                                onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_bairro')"
                                value="<?php echo $resultado10['agr_bairro']; ?>" id='bairroagressor'>
                            </div>

                          </div>
                        </div>


                        <div class="row">
                          <div class="col-sm-4">

                            <div class="form-group">
                              <label for="agr_referencia">Referência</label>
                              <input name="referenciaagressor" type="text" class="form-control"
                                onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_referencia')"
                                value="<?php echo $resultado10['agr_referencia']; ?>" id='referenciaagressor'>
                            </div>

                          </div>
                          <div class="col-sm-4">

                            <div class="form-group">
                              <label for="agr_cidade">Cidade</label>
                              <input name="cidadeagressor" type="text" class="form-control"
                                onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_cidade')"
                                value="<?php echo $resultado10['agr_cidade']; ?>" id='cidadeagressor'>
                            </div>

                          </div>
                          <div class="col-sm-4">

                            <div class="form-group">
                              <label for="agr_estado">Estado</label>
                              <input name="estadoagressor" type="text" class="form-control"
                                onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_estado')"
                                value="<?php echo $resultado10['agr_estado']; ?>" id='estadoagressor'>
                            </div>

                          </div>
                        </div>


                        <div class="row">
                          <div class="col-sm-4">

                            <div class="form-group">
                              <label for="agr_telefone1">Telefone Residência</label>
                              <input name="text" type="text" class="form-control"
                                onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_telefone1')"
                                value="<?php echo $resultado10['agr_telefone1']; ?>">
                            </div>

                          </div>
                          <div class="col-sm-4">

                            <div class="form-group">
                              <label for="agr_telefone2">Telefone Celular</label>
                              <input name="text" type="text" class="form-control"
                                onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_telefone2')"
                                value="<?php echo $resultado10['agr_telefone2']; ?>">
                            </div>

                          </div>
                          <div class="col-sm-4">

                            <div class="form-group">
                              <label for="agr_telefone3">Telefone Recado</label>
                              <input name="text" type="text" class="form-control"
                                onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_telefone3')"
                                value="<?php echo $resultado10['agr_telefone3']; ?>">
                            </div>

                          </div>
                        </div>


                        <?php echo geraselectadversa(11, $codigo, 'agr_grau_instrucao', 'Grau de Instrução'); ?>


                        <div class="checkbox">
                          <label>
                            <input type="checkbox" name="opt1" value="1"
                              onchange="submetetruefalseadversa(this.checked, <?php echo $adversa ?>, 'agr_historico_de_agressoes')"
                              <?php if ($resultado10['agr_historico_de_agressoes'] == '1')
                                echo "checked='checked'"; ?>
                              value="sim"> Tem histórico de agressão em se tratando de outros relacionamentos?</a>
                          </label>
                        </div>
                        <div class="checkbox">
                          <label>
                            <input type="checkbox" name="opt1" value="1"
                              onchange="submetetruefalseadversa(this.checked, <?php echo $adversa ?>, 'agr_sob_medida_protetiva')"
                              <?php if ($resultado10['agr_sob_medida_protetiva'] == '1')
                                echo "checked='checked'"; ?>
                              value="sim"> Está sob Medida Protetiva de Urgência?</a>
                          </label>
                        </div>
                        <div class="checkbox">
                          <label>
                            <input type="checkbox" name="opt1" value="1"
                              onchange="submetetruefalseadversa(this.checked, <?php echo $adversa ?>, 'agr_sob_medida_protetiva_usuaria')"
                              <?php if ($resultado10['agr_sob_medida_protetiva_usuaria'] == '1')
                                echo "checked='checked'"; ?> value="sim"> Se está sob medida protetiva, a vitima é a usuária?</a>
                          </label>
                        </div>
                        <div class="checkbox">
                          <label>
                            <input id="usapsicoativa" value="sim" type="checkbox" name="opt1" value="1"
                              onchange="submetetruefalseadversa(this.checked, <?php echo $adversa ?>, 'agr_drogas')" <?php if ($resultado10['agr_drogas'] == '1')
                                   echo "checked='checked'"; ?> value="sim"> Faz uso de
                            alguma substância psicoativa?</a>
                          </label>
                        </div>

                        <div class="row">
                          <div class="col-sm-4">

                            <div class="form-group" data-visibility-target="usapsicoativa" data-visibility-value="sim">
                              <label for="agr_drogas_quais">Se sim, quais as substâncias?</label>
                              <input name="text" type="text" class="form-control"
                                onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_drogas_quais')"
                                value="<?php echo $resultado10['agr_drogas_quais']; ?>">
                            </div>


                          </div>
                          <div class="col-sm-4">

                            <?php echo geraselectadversa(28, $codigo, 'agr_ocupacao', 'Ocupação'); ?>



                          </div>
                          <div class="col-sm-4">


                            <?php echo geraselectadversa(16, $adversa, 'agr_faixa_salarial', 'Faixa Salarial'); ?>



                          </div>
                        </div>

                        <div class="row">
                          <div class="col-sm-4">

                            <div class="form-group" data-visibility-target="agr_ocupacao"
                              data-visibility-value="212,215,217">
                              <label for="agr_ocupacao_empresa">Empresa onde trabalha</label>
                              <input name="text" type="text" class="form-control"
                                onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_ocupacao_empresa')"
                                value="<?php echo $resultado10['agr_ocupacao_empresa']; ?>">
                            </div>

                          </div>
                          <div class="col-sm-4">

                            <div class="form-group" data-visibility-target="agr_ocupacao"
                              data-visibility-value="212,215,217">
                              <label for="agr_ocupacao_funcao">Função no trabalho</label>
                              <input name="text" type="text" class="form-control"
                                onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_ocupacao_funcao')"
                                value="<?php echo $resultado10['agr_ocupacao_funcao']; ?>">
                            </div>

                          </div>
                          <div class="col-sm-4">


                            <div class="form-group" data-visibility-target="agr_ocupacao"
                              data-visibility-value="212,215,217">
                              <label for="agr_ocupacao_telefone">Telefone da Empresa</label>
                              <input name="text" type="text" class="form-control"
                                onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_ocupacao_telefone')"
                                value="<?php echo $resultado10['agr_ocupacao_telefone']; ?>">
                            </div>



                          </div>
                        </div>





                      </form>


                    </div>
                  </div>



                  <div class="card card-danger card-outline">
                    <div class="card-header">
                      <h3 class="card-title">
                        <i class="fas"></i>
                        Detalhes da Violência
                      </h3>
                    </div>
                    <div class="card-body">


                      <?php echo geracheckboxadversa(22, $adversa, "Que tipo de violência sofre atualmente?", "|") ?>

                      <?php echo geracheckboxadversa(23, $adversa, "Em que local ocorreu a violência?", "|") ?>


                      <div class="row">
                        <div class="col-sm-4">


                          <div class="form-group">
                            <label for="agr_data_inicio_agressoes">Quando ocorreu/iniciaram as agressões</label>
                            <input name="text" type="date" class="form-control"
                              onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_data_inicio_agressoes')"
                              value="<?php echo $resultado10['agr_data_inicio_agressoes']; ?>">
                          </div>


                        </div>
                        <div class="col-sm-4">

                          <?php echo geraselectadversa(36, $adversa, 'agr_frequencia', 'Frequência das Agressões'); ?>


                        </div>
                        <div class="col-sm-4">

                          <?php echo geraselectadversa(37, $adversa, 'agr_relacao', 'Qual a relação do agressor (a) com a usuária?'); ?>

                        </div>
                      </div>

                      <?php echo geracheckboxadversa(29, $adversa, "Justificativa por parte do agressor para Violência", "|") ?>



                      <?php echo geracheckboxadversa(30, $adversa, "Quais eram as condições físicas e emocionais do agressor quando praticou a violência?", "|") ?>




                    </div>
                  </div>


                  <div class="card card-danger card-outline">
                    <div class="card-header">
                      <h3 class="card-title">
                        <i class="fas"></i>
                        Boletim de Ocorrência
                      </h3>
                    </div>
                    <div class="card-body">


                      <div class="checkbox">
                        <label>
                          <input id="fezbo" value="sim" type="checkbox" name="opt1" value="1"
                            onchange="submetetruefalseadversa(this.checked, <?php echo $adversa ?>, 'agr_fez_bo')" <?php if ($resultado10['agr_fez_bo'] == '1')
                                 echo "checked='checked'"; ?> value="sim"> Fez a
                          notificação da violência atual sofrida</a>
                        </label>
                      </div>


                      <div class="row" data-visibility-target="fezbo" data-visibility-value="sim">
                        <div class="col-sm-4">

                          <div class="form-group">
                            <label for="agr_numero_bo">Número B.O.</label>
                            <input name="text" type="text" class="form-control"
                              onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_numero_bo')"
                              value="<?php echo $resultado10['agr_numero_bo']; ?>">
                          </div>


                        </div>
                        <div class="col-sm-4">

                          <div class="form-group">
                            <label for="agr_data_bo">Data do B.O.</label>
                            <input name="text" type="date" class="form-control"
                              onblur="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_data_bo')"
                              value="<?php echo $resultado10['agr_data_bo']; ?>">
                          </div>


                        </div>
                        <div class="col-sm-4">

                          <div class="form-group">
                            <label for="agr_local_bo">Local B.O.</label>
                            <input name="text" type="text" class="form-control"
                              onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_local_bo')"
                              value="<?php echo $resultado10['agr_local_bo']; ?>">
                          </div>

                        </div>
                      </div>

                      <div class="form-group">
                        <label for="agr_motivo_sem_bo">Por que não fez B.O.?</label>
                        <input name="text" type="text" class="form-control"
                          onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_motivo_sem_bo')"
                          value="<?php echo $resultado10['agr_motivo_sem_bo']; ?>">
                      </div>

                      <div class="checkbox">
                        <label>
                          <input id="fezoitiva" type="checkbox" name="opt1" value="1"
                            onchange="submetetruefalseadversa(this.checked, <?php echo $adversa ?>, 'agr_oitiva')" <?php if ($resultado10['agr_oitiva'] == '1')
                                 echo "checked='checked'"; ?> value="sim"> Tem agendamento
                          de Oitiva</a>
                        </label>
                      </div>

                      <div class="row" data-visibility-target="fezoitiva" data-visibility-value="sim">
                        <div class="col-sm-6">

                          <div class="form-group">
                            <label for="agr_oitiva_data">Data e Hora da Oitiva</label>
                            <input name="text" type="datetime-local" class="form-control"
                              onblur="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_oitiva_data')"
                              value="<?php echo $resultado10['agr_oitiva_data2']; ?>">
                          </div>


                        </div>
                        <div class="col-sm-6">

                          <div class="form-group">
                            <label for="agr_oitiva_local">Local da Oitiva</label>
                            <input name="text" type="text" class="form-control"
                              onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_oitiva_local')"
                              value="<?php echo $resultado10['agr_oitiva_local']; ?>">
                          </div>

                        </div>
                      </div>

                      <div class="form-group">
                        <label for="agr_motivo_sem_oitiva">Por que não fez Oitiva?</label>
                        <input name="text" type="text" class="form-control"
                          onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_motivo_sem_oitiva')"
                          value="<?php echo $resultado10['agr_motivo_sem_oitiva']; ?>">
                      </div>


                      <div class="row">
                        <div class="col-sm-6">

                          <div class="checkbox">
                            <label>
                              <input type="checkbox" name="opt1" value="1"
                                onchange="submetetruefalseadversa(this.checked, <?php echo $adversa ?>, 'agr_corpo_delito')"
                                <?php if ($resultado10['agr_corpo_delito'] == '1')
                                  echo "checked='checked'"; ?> value="sim">
                              Exame de Corpo de Delito no ITEP</a>
                            </label>
                          </div>


                        </div>
                        <div class="col-sm-6">

                          <div class="checkbox">
                            <label>
                              <input type="checkbox" name="opt1" value="1"
                                onchange="submetetruefalseadversa(this.checked, <?php echo $adversa ?>, 'agr_exame_itep')"
                                <?php if ($resultado10['agr_exame_itep'] == '1')
                                  echo "checked='checked'"; ?> value="sim">
                              Exame de Conjunção Carnal no ITEP</a>
                            </label>
                          </div>

                        </div>
                      </div>

                      <div class="form-group">
                        <label for="agr_exame_itep_motivo">Por que não fez exame no ITEP/RN (se não fez)?</label>
                        <input name="text" type="text" class="form-control"
                          onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_exame_itep_motivo')"
                          value="<?php echo $resultado10['agr_exame_itep_motivo']; ?>">
                      </div>

                      <div class="checkbox">
                        <label>
                          <input id="profilaxia" value="sim" type="checkbox" name="opt1" value="1"
                            onchange="submetetruefalseadversa(this.checked, <?php echo $adversa ?>, 'agr_profilaxia')"
                            <?php if ($resultado10['agr_profilaxia'] == '1')
                              echo "checked='checked'"; ?> value="sim"> Foi a
                          alguma unidade de Saúde para fazer a Profilaxia de Emergência</a>
                        </label>
                      </div>


                      <div class="row" data-visibility-target="profilaxia" data-visibility-value="sim">
                        <div class="col-sm-6">

                          <div class="form-group">
                            <label for="agr_profilaxia_local">Local da profilaxia</label>
                            <input name="text" type="text" class="form-control"
                              onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_profilaxia_local')"
                              value="<?php echo $resultado10['agr_profilaxia_local']; ?>">
                          </div>


                        </div>
                        <div class="col-sm-6">

                          <div class="form-group">
                            <label for="agr_profilaxia_data">Data da profilaxia</label>
                            <input name="text" type="date" class="form-control"
                              onblur="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_profilaxia_data')"
                              value="<?php echo $resultado10['agr_profilaxia_data']; ?>">
                          </div>

                        </div>
                      </div>



                      <div class="row">
                        <div class="col-sm-6">


                          <?php echo geraselectadversa(31, $adversa, 'agr_itep_natureza', 'Caso tenha feito algum exame no ITEP/RN, foi detectada a natureza da lesão?'); ?>

                        </div>
                        <div class="col-sm-6">


                          <?php echo geraselectadversa(32, $adversa, 'agr_itep_arma', 'A lesão foi causada por algum tipo de arma?'); ?>


                        </div>
                      </div>



                    </div>
                  </div>


                  <div class="card card-danger card-outline">
                    <div class="card-header">
                      <h3 class="card-title">
                        <i class="fas"></i>
                        Medida Protetiva
                      </h3>
                    </div>
                    <div class="card-body">


                      <div class="row">
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="agr_numero_medida">Nº do Processo da medida protetiva</label>
                            <input name="text" type="text" class="form-control"
                              onchange="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_numero_medida')"
                              value="<?php echo $resultado10['agr_numero_medida']; ?>">
                          </div>

                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="agr_data_medida">Data da medida protetiva</label>
                            <input name="text" type="date" class="form-control"
                              onblur="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_data_medida')"
                              value="<?php echo $resultado10['agr_data_medida']; ?>">
                          </div>
                        </div>
                      </div>


                      <div class="row">
                        <div class="col-sm-6">
                          <div class="checkbox">
                            <label>
                              <input id="cienciaofendida" value="sim" type="checkbox" name="opt1" value="1"
                                onchange="submetetruefalseadversa(this.checked, <?php echo $adversa ?>, 'agr_ciencia_ofendida')"
                                <?php if ($resultado10['agr_ciencia_ofendida'] == '1')
                                  echo "checked='checked'"; ?>
                                value="sim"> Ciência da Ofendida e devolução ao Juizado</a>
                            </label>
                          </div>

                        </div>
                        <div class="col-sm-6" data-visibility-target="cienciaofendida" data-visibility-value="sim">
                          <div class="form-group">
                            <label for="agr_ciencia_ofendida_data">Data da ciência da Ofendida</label>
                            <input name="text" type="date" class="form-control"
                              onblur="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_ciencia_ofendida_data')"
                              value="<?php echo $resultado10['agr_ciencia_ofendida_data']; ?>">
                          </div>
                        </div>
                      </div>


                      <div class="row">
                        <div class="col-sm-6">
                          <div class="checkbox">
                            <label>
                              <input id="cienciaagressor" value="sim" type="checkbox" name="opt1" value="1"
                                onchange="submetetruefalseadversa(this.checked, <?php echo $adversa ?>, 'agr_ciencia_agressor')"
                                <?php if ($resultado10['agr_ciencia_agressor'] == '1')
                                  echo "checked='checked'"; ?>
                                value="sim"> Ciência do Agressor</a>
                            </label>
                          </div>

                        </div>
                        <div class="col-sm-6" data-visibility-target="cienciaagressor" data-visibility-value="sim">
                          <div class="form-group">
                            <label for="agr_ciencia_agressor_data">Data da ciência do Agressor</label>
                            <input name="text" type="date" class="form-control"
                              onblur="submetevaloradversa(this.value, <?php echo $adversa ?>, 'agr_ciencia_agressor_data')"
                              value="<?php echo $resultado10['agr_ciencia_agressor_data']; ?>">
                          </div>
                        </div>
                      </div>

                      <?php echo geracheckboxadversa(33, $adversa, "Medidas Protetivas para o Agressor", "<br>") ?>
                      <?php echo geracheckboxadversa(33, $adversa, "Medidas Protetivas para a Ofendida", "<br>") ?>




                      <!--<div class="card-footer row justify-content-md-center justify-content-sm-center justify-content-center">-->
                      <!--  <button onclick="return verifica()" type="submit" class="btn btn-primary">Salvar Alterações</button>-->
                      <!--</div>-->


                    </div>
                  </div>


                <?php } ?>

              </div>



              <div class="<?php if ($aba == "pessoas")
                echo "active" ?> tab-pane" id="social">
                <?php if (@$msg == 2)
                echo
                  "<div id='alerta' class='alert alert-success' role='alert'>
                            Pessoa adicionada ao cadastro familiar!
                        </div>";
              ?>
                <div class="card card-secondary">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas"></i>
                      Cadastro Familiar
                    </h3>
                  </div>
                  <div class="card-body">

                    <div id="example2_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                      <div class="row">
                        <div class="col-sm-12 col-md-6"></div>
                        <div class="col-sm-12 col-md-6"></div>
                      </div>
                      <div class="row">
                        <div class="col-sm-12">
                          <table class="table table-hover table-bordered dataTable dtr-inline no-footer" id="example2"
                            role="grid">
                            <thead>
                              <tr role="row">
                                <th class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                                  aria-sort="ascending" aria-label="Nome: activate to sort column descending">Nome</th>
                                <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                                  aria-label="Grau: activate to sort column ascending">Grau</th>
                                <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                                  aria-label="Idade: activate to sort column ascending">Idade</th>
                                <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                                  aria-label="Ação: activate to sort column ascending">Ação</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php while ($resultado2 = $consulta2->fetch_assoc()) { ?>
                                <tr role="row" class="odd">
                                  <td class="align-middle" tabindex="0" class="sorting_1">
                                    <?php echo $resultado2['pes_nome'] ?>
                                  </td>
                                  <td class="align-middle"><?php echo $resultado2['alt_alternativa'] ?></td>
                                  <td class="align-middle">
                                    <?php if ($resultado2['idade'] != '')
                                      echo $resultado2['idade'] . ' anos';
                                    else
                                      echo '-' ?>
                                    </td>
                                    <td class="align-middle"><a
                                        href="?codigo=<?php echo $codigo; ?>&pessoa=<?php echo $resultado2['pes_codigo'] ?>"><button
                                        type="button" class="btn btn-block bg-gradient-primary btn-xs"><i
                                          class="fas fa-search"></i> Ver</button></a></td>
                                </tr>
                              <?php } ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-12 col-md-5"></div>
                        <div class="col-sm-12 col-md-7"></div>
                      </div>
                    </div>

                  </div>
                  <div class="card-footer justify-content-md-center text-center">
                    <button type="submit" class="btn btn-success" data-toggle="modal"
                      data-target="#modal-social">Adicionar nova pessoa</button>
                  </div>


                </div>
                <?php if (isset($_GET['pessoa'])) { ?>
                  <div class="card card-danger card-outline">
                    <div class="card-header">
                      <h3 class="card-title">
                        <i class="fas"></i>
                        Detalhes da Pessoa
                      </h3>
                    </div>
                    <div class="card-body">

                      <form class="form-horizontal">


                        <div class="row">
                          <div class="col-sm-6">

                            <div class="form-group">
                              <label for="pes_nome">Nome</label>
                              <input name="nome" type="text" class="form-control"
                                onchange="submetevalorpessoa(this.value, <?php echo $pessoa ?>, 'pes_nome')"
                                value="<?php echo $resultado7['pes_nome']; ?>">
                            </div>

                          </div>
                          <div class="col-sm-6">


                            <div class="form-group">
                              <label for="pes_data_nasc">Data de Nascimento</label>
                              <input name="datanascimento" type="date" class="form-control"
                                onblur="submetevalorpessoa(this.value, <?php echo $pessoa ?>, 'pes_data_nasc')"
                                value="<?php echo $resultado7['pes_data_nasc']; ?>">
                            </div>

                          </div>
                        </div>

                        <div class="row">
                          <div class="col-sm-6">

                            <?php echo geraselectpessoa(27, $pessoa, 'pes_grau_parentesco', 'Grau de Parentesco'); ?>

                          </div>
                          <div class="col-sm-6">

                            <div class="form-group">
                              <label for="pes_telefone">Telefone</label>
                              <input name="telefone" type="text" class="form-control"
                                onchange="submetevalorpessoa(this.value, <?php echo $pessoa ?>, 'pes_telefone')"
                                value="<?php echo $resultado7['pes_telefone']; ?>">
                            </div>

                          </div>
                        </div>





                        <div class="checkbox">
                          <label>
                            <input type="checkbox" name="opt1" value="1"
                              onchange="submetetruefalsepessoa(this.checked, <?php echo $pessoa ?>, 'pes_mesma_casa')"
                              <?php if ($resultado7['pes_mesma_casa'] == '1')
                                echo "checked='checked'"; ?>> Mora na mesma
                            casa?
                          </label>
                        </div>
                        <div class="checkbox">
                          <label>
                            <input id="privacao" value="sim" type="checkbox" name="opt1" value="1"
                              onchange="submetetruefalsepessoa(this.checked, <?php echo $pessoa ?>, 'pes_privacao_liberdade')"
                              <?php if ($resultado7['pes_privacao_liberdade'] == '1')
                                echo "checked='checked'"; ?>> Está em
                            privação de liberdade? <span style="font-weight: normal;">(Regime Fechado, Semi aberto,
                              Aguardando Julgamento, Tornozeleira, Condenado)</span>
                          </label>
                        </div>
                        <div class="form-group" data-visibility-target="privacao" data-visibility-value="sim">
                          <label for="pes_privacao_liberdade_situacao">Situação</label>
                          <input name="situacao1" type="text" class="form-control"
                            onchange="submetevalorpessoa(this.value, <?php echo $pessoa ?>, 'pes_privacao_liberdade_situacao')"
                            value="<?php echo $resultado7['pes_privacao_liberdade']; ?>">
                        </div>
                        <div class="checkbox">
                          <label>
                            <input id="adolescente" value="sim" type="checkbox" name="opt1" value="1"
                              onchange="submetetruefalsepessoa(this.checked, <?php echo $pessoa ?>, 'pes_medida_socioeducativa')"
                              <?php if ($resultado7['pes_medida_socioeducativa'] == '1')
                                echo "checked='checked'"; ?>>
                            Adolescentes em cumprimento de Medidas Socieducativas? <span
                              style="font-weight: normal;">(Advertência,
                              Prestação de Serviço à Comunidade, Liberdade Assistida, Obrigação de reparar dano, semi
                              liberdade ou internação [ECA Art. 112])</span>
                          </label>
                        </div>
                        <div class="form-group" data-visibility-target="adolescente" data-visibility-value="sim">
                          <label for="pes_medida_socioeducativa_situacao">Situação</label>
                          <input name="situacao2" type="text" class="form-control"
                            onchange="submetevalorpessoa(this.value, <?php echo $pessoa ?>, 'pes_medida_socioeducativa_situacao')"
                            value="<?php echo $resultado7['pes_medida_socioeducativa_situacao']; ?>">
                        </div>
                        <div class="checkbox">
                          <label>
                            <input id="acolhimento" value="sim" type="checkbox" name="opt1" value="1"
                              onchange="submetetruefalsepessoa(this.checked, <?php echo $pessoa ?>, 'pes_acolhimento')"
                              <?php if ($resultado7['pes_acolhimento'] == '1')
                                echo "checked='checked'"; ?>> Encontra-se em
                            acolhimento institucional? <span style="font-weight: normal;">(Instituição de longa
                              Permanência para Idosos,
                              Casa Lar, Acolhimento Institucional, Casa de Passagem, Residência Inclusiva.)</span>
                          </label>
                        </div>
                        <div class="form-group" data-visibility-target="acolhimento" data-visibility-value="sim">
                          <label for="pes_acolhimento_situacao">Situação</label>
                          <input name="situacao3" type="text" class="form-control"
                            onchange="submetevalorpessoa(this.value, <?php echo $pessoa ?>, 'pes_acolhimento_situacao')"
                            value="<?php echo $resultado7['pes_acolhimento_situacao']; ?>">
                        </div>
                        <div class="checkbox">
                          <label>
                            <input id="internado" value="sim" type="checkbox" name="opt1" value="1"
                              onchange="submetetruefalsepessoa(this.checked, <?php echo $pessoa ?>, 'pes_internado')"
                              <?php if ($resultado7['pes_internado'] == '1')
                                echo "checked='checked'"; ?>> Encontra-se
                            institucionalizado/internado?
                          </label>
                        </div>
                        <div class="form-group" data-visibility-target="internado" data-visibility-value="sim">
                          <label for="pes_medida_socioeducativa_situacao">Situação</label>
                          <input name="situacao4" type="text" class="form-control"
                            onchange="submetevalorpessoa(this.value, <?php echo $pessoa ?>, 'pes_medida_socioeducativa_situacao')"
                            value="<?php echo $resultado7['pes_internado_situacao']; ?>">
                        </div>
                        <div class="checkbox">
                          <label>
                            <input id="outrasdoencas" value="sim" type="checkbox" name="opt1" value="1"
                              onchange="submetetruefalsepessoa(this.checked, <?php echo $pessoa ?>, 'pes_doenca')" <?php if ($resultado7['pes_doenca'] == '1')
                                   echo "checked='checked'"; ?>> Com alguma doença
                          </label>
                        </div>
                        <div class="form-group" data-visibility-target="outrasdoencas" data-visibility-value="sim">
                          <label for="pes_doenca_detalhes">Qual doença e como interfere na dinâmica familiar?</label>
                          <input name="doencas" type="text" class="form-control"
                            onchange="submetevalorpessoa(this.value, <?php echo $pessoa ?>, 'pes_doenca_detalhes')"
                            value="<?php echo $resultado7['pes_doenca_detalhes']; ?>">
                        </div>
                        <div class="checkbox">
                          <label>
                            <input id="deficiencia" value="sim" type="checkbox" name="opt1" value="1"
                              onchange="submetetruefalsepessoa(this.checked, <?php echo $pessoa ?>, 'pes_deficiencia')"
                              <?php if ($resultado7['pes_deficiencia'] == '1')
                                echo "checked='checked'"; ?>> Com algum tipo
                            de deficiência
                          </label>
                        </div>
                        <div data-visibility-target="deficiencia" data-visibility-value="sim">

                          <?php geracheckboxpessoa(25, $pessoa, "Deficiências", "<br>") ?>

                          <div class="form-group">
                            <label for="pes_deficiencia_detalhes">Qual tipo de deficiência e como interfere na dinâmica
                              familiar?</label>
                            <input name="deficiencia" type="text" class="form-control"
                              onchange="submetevalorpessoa(this.value, <?php echo $pessoa ?>, 'pes_deficiencia_detalhes')"
                              value="<?php echo $resultado7['pes_deficiencia_detalhes']; ?>">
                          </div>
                        </div>
                        <div class="checkbox">
                          <label>
                            <input id="gestante" value="sim" type="checkbox" name="opt1" value="1"
                              onchange="submetetruefalsepessoa(this.checked, <?php echo $pessoa ?>, 'pes_gestante')" <?php if ($resultado7['pes_gestante'] == '1')
                                   echo "checked='checked'"; ?>> Gestante
                          </label>
                        </div>
                        <div class="form-group" data-visibility-target="gestante" data-visibility-value="sim">
                          <label for="pes_gestante_detalhes">Faz acompanhamento médico? Como essa situação interfere na
                            dinâmica familiar?</label>
                          <input name="gestante" type="text" class="form-control"
                            onchange="submetevalorpessoa(this.value, <?php echo $pessoa ?>, 'pes_gestante_detalhes')"
                            value="<?php echo $resultado7['pes_gestante_detalhes']; ?>">
                        </div>
                        <div class="checkbox">
                          <label>
                            <input id="usadrogas" value="sim" type="checkbox" name="opt1" value="1"
                              onchange="submetetruefalsepessoa(this.checked, <?php echo $pessoa ?>, 'pes_drogas')" <?php if ($resultado7['pes_drogas'] == '1')
                                   echo "checked='checked'"; ?>> Faz uso abusivo de
                            cigarro,
                            substâncias psicoativas lícitas e ilícitas, jogos, etc?
                          </label>
                        </div>
                        <div class="form-group" data-visibility-target="usadrogas" data-visibility-value="sim">
                          <label for="pes_drogas_detalhes">Se sim, de que forma interfere na vida cotidiana (relações
                            sociais, familiares, trabalho, escola)</label>
                          <input name="drogas" type="text" class="form-control"
                            onchange="submetevalorpessoa(this.value, <?php echo $pessoa ?>, 'pes_drogas_detalhes')"
                            value="<?php echo $resultado7['pes_drogas_detalhes']; ?>">
                        </div>
                        <div class="checkbox">
                          <label>
                            <input id="idoso" value="sim" type="checkbox" name="opt1" value="1"
                              onchange="submetetruefalsepessoa(this.checked, <?php echo $pessoa ?>, 'pes_cuidador')" <?php if ($resultado7['pes_cuidador'] == '1')
                                   echo "checked='checked'"; ?>> Devido ao
                            envelhecimento,
                            necessita de cuidados constantes de outras pessoas.
                          </label>
                        </div>
                        <div class="form-group" data-visibility-target="idoso" data-visibility-value="sim">
                          <label for="pes_cuidador_detalhes">Se sim, como essa situação interfere na dinâmica familiar?
                          </label>
                          <input name="idoso" type="text" class="form-control"
                            onchange="submetevalorpessoa(this.value, <?php echo $pessoa ?>, 'pes_cuidador_detalhes')"
                            value="<?php echo $resultado7['pes_cuidador_detalhes']; ?>">
                        </div>
                        <div class="checkbox">
                          <label>
                            <input id="estudante" value="sim" type="checkbox" name="opt1" value="1"
                              onchange="submetetruefalsepessoa(this.checked, <?php echo $pessoa ?>, 'pes_estudante')"
                              <?php if ($resultado7['pes_estudante'] == '1')
                                echo "checked='checked'"; ?>> Estudante?
                          </label>
                        </div>


                        <div class="row" data-visibility-target="estudante" data-visibility-value="sim">
                          <div class="col-sm-4">

                            <div class="form-group">
                              <label for="pes_estudante_escola">Escola</label>
                              <input name="escola" type="text" class="form-control"
                                onchange="submetevalorpessoa(this.value, <?php echo $pessoa ?>, 'pes_estudante_escola')"
                                value="<?php echo $resultado7['pes_estudante_escola']; ?>">
                            </div>

                          </div>
                          <div class="col-sm-4">


                            <div class="form-group">
                              <label for="pes_estudante_serie">Série</label>
                              <input name="serieescola" type="text" class="form-control"
                                onchange="submetevalorpessoa(this.value, <?php echo $pessoa ?>, 'pes_estudante_serie')"
                                value="<?php echo $resultado7['pes_estudante_serie']; ?>">
                            </div>

                          </div>
                          <div class="col-sm-4">


                            <div class="form-group">
                              <label for="pes_estudante_turno">Horário</label>
                              <input name="turnoescola" type="text" class="form-control"
                                onchange="submetevalorpessoa(this.value, <?php echo $pessoa ?>, 'pes_estudante_turno')"
                                value="<?php echo $resultado7['pes_estudante_turno']; ?>">
                            </div>

                          </div>
                        </div>



                        <!--<div class="card-footer row justify-content-md-center justify-content-sm-center justify-content-center">-->
                        <!--  <button onclick2="return verifica()" type="submit" class="btn btn-primary">Salvar Alterações</button>-->
                        <!--</div>-->

                      </form>
                    </div>
                  </div>

                <?php } ?>


              </div>




              <div class="tab-pane" id="saude">
                <form class="form-horizontal">


                  <div class="card card-danger card-outline">
                    <div class="card-header">
                      <h3 class="card-title">
                        <i class="fas"></i>
                        Saúde
                      </h3>
                    </div>
                    <div class="card-body">



                      <?php echo geracheckbox(25, $codigo, "Condição de Saúde", "|"); ?>


                      <?php echo geracheckbox(26, $codigo, "Doenças Infecciosas", "|"); ?>

                      <div class="form-group" data-visibility-target="a178" data-visibility-value="1">
                        <label for="mul_outras_doencas">Outras doenças:</label>
                        <input name="text" type="text" class="form-control"
                          onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_outras_doencas')"
                          value="<?php echo $resultado['mul_outras_doencas']; ?>">
                      </div>
                      <div class="form-group">
                        <label for="mul_alergias">Alergias:</label>
                        <input name="text" type="text" class="form-control"
                          onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_alergias')"
                          value="<?php echo $resultado['mul_alergias']; ?>">
                      </div>

                      <div class="checkbox">
                        <label>
                          <input type="checkbox" id="medpsico" name="opt1" value="sim"
                            onchange="submetetruefalse(this.checked, <?php echo $codigo ?>, 'mul_medicamento_psico')"
                            <?php if ($resultado['mul_medicamento_psico'] == '1')
                              echo "checked='checked'"; ?>> Faz uso de
                          algum tipo de medicamento continuado ou psicotrópico
                        </label>
                      </div>


                      <div class="row" data-visibility-target="medpsico" data-visibility-value="sim">
                        <div class="col-sm-6">


                          <div class="form-group">
                            <label for="mul_medicamento_psico_qual">Se sim, qual</label>
                            <input name="medicamento" type="text" class="form-control"
                              onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_medicamento_psico_qual')"
                              value="<?php echo $resultado['mul_medicamento_psico_qual']; ?>">
                          </div>


                        </div>
                        <div class="col-sm-6">

                          <div class="form-group">
                            <label for="mul_medicamento_psico_data">Data aproximada em que iniciou o uso</label>
                            <input name="datamedicamento" type="date" class="form-control"
                              onblur="submetevalor(this.value, <?php echo $codigo ?>, 'mul_medicamento_psico_data')"
                              value="<?php echo $resultado['mul_medicamento_psico_data']; ?>">
                          </div>

                        </div>
                      </div>

                      <div class="checkbox" data-visibility-target="medpsico" data-visibility-value="sim">
                        <label>
                          <input type="checkbox" name="opt1" value="1"
                            onchange="submetetruefalse(this.checked, <?php echo $codigo ?>, 'mul_atualmente_em_uso')"
                            <?php if ($resultado['mul_atualmente_em_uso'] == '1')
                              echo "checked='checked'"; ?>> Está com o
                          medicamento no momento
                        </label>
                      </div>
                      <div class="checkbox">
                        <label>
                          <input id="psiqui" value="sim" type="checkbox" name="opt1" value="1"
                            onchange="submetetruefalse(this.checked, <?php echo $codigo ?>, 'mul_psiquiatra')" <?php if ($resultado['mul_psiquiatra'] == '1')
                                 echo "checked='checked'"; ?>> Já teve algum
                          atendimento Psiquiátrico
                        </label>
                      </div>
                      <div class="form-group" data-visibility-target="psiqui" data-visibility-value="sim">
                        <label for="mul_psiquiatra_data">Data do atendimento psiquiátrico?</label>
                        <input name="datapsiquiatra" type="date" class="form-control"
                          onblur="submetevalor(this.value, <?php echo $codigo ?>, 'mul_psiquiatra_data')"
                          value="<?php echo $resultado['mul_psiquiatra_data']; ?>">
                      </div>
                      <div class="checkbox" data-visibility-target="psiqui" data-visibility-value="sim">
                        <label>
                          <input id="internacao" value="sim" type="checkbox" name="opt1" value="1"
                            onchange="submetetruefalse(this.checked, <?php echo $codigo ?>, 'mul_psiquiatra_internacao')"
                            <?php if ($resultado['mul_psiquiatra_internacao'] == '1')
                              echo "checked='checked'"; ?>>
                          Necessitou de Internação
                        </label>
                      </div>


                      <div class="row" data-visibility-target="psiqui" data-visibility-value="sim">
                        <div class="col-sm-6">

                          <div class="form-group" data-visibility-target="internacao" data-visibility-value="sim">
                            <label for="mul_psiquiatra_dias">Quantidade de dias internada</label>
                            <input name="diasinternada" type="number" class="form-control"
                              onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_psiquiatra_dias')"
                              value="<?php echo $resultado['mul_psiquiatra_dias']; ?>">
                          </div>


                        </div>
                        <div class="col-sm-6">

                          <div class="form-group" data-visibility-target="internacao" data-visibility-value="sim">
                            <label for="mul_psiquiatra_local">Local da Internação</label>
                            <input name="localinternacao" type="text" class="form-control"
                              onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_psiquiatra_local')"
                              value="<?php echo $resultado['mul_psiquiatra_local']; ?>">
                          </div>

                        </div>
                      </div>

                      <div class="checkbox">
                        <label>
                          <input id="psicologico" value="sim" type="checkbox" name="opt1" value="1"
                            onchange="submetetruefalse(this.checked, <?php echo $codigo ?>, 'mul_psicologo')" <?php if ($resultado['mul_psicologo'] == '1')
                                 echo "checked='checked'"; ?>> Já realizou
                          acompanhamento psicológico?
                        </label>
                      </div>


                      <div class="row" data-visibility-target="psicologico" data-visibility-value="sim">
                        <div class="col-sm-4">

                          <div class="form-group">
                            <label for="mul_psicologo_data">Se sim, quando</label>
                            <input name="datapsicologo" type="date" class="form-control"
                              onblur="submetevalor(this.value, <?php echo $codigo ?>, 'mul_psicologo_data')"
                              value="<?php echo $resultado['mul_psicologo_data']; ?>">
                          </div>


                        </div>
                        <div class="col-sm-4">

                          <div class="form-group">
                            <label for="mul_psicologo_local">Local do atendimento</label>
                            <input name="localpsicologo" type="text" class="form-control"
                              onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_psicologo_local')"
                              value="<?php echo $resultado['mul_psicologo_local']; ?>">
                          </div>


                        </div>
                        <div class="col-sm-4">

                          <div class="form-group">
                            <label for="mul_psicologo_motivo">Motivo</label>
                            <input name="motivopsicologo" type="text" class="form-control"
                              onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_psicologo_motivo')"
                              value="<?php echo $resultado['mul_psicologo_motivo']; ?>">
                          </div>

                        </div>
                      </div>

                      <div class="checkbox">
                        <label>
                          <input id="psicoativa" value="sim" type="checkbox" name="opt1" value="1"
                            onchange="submetetruefalse(this.checked, <?php echo $codigo ?>, 'mul_drogas')" <?php if ($resultado['mul_drogas'] == '1')
                                 echo "checked='checked'"; ?>> Faz uso de alguma substância
                          psicoativa?
                        </label>
                      </div>


                      <div class="row" data-visibility-target="psicoativa" data-visibility-value="sim">
                        <div class="col-sm-6">

                          <div class="form-group">
                            <label for="mul_droga_data">Se sim, quando iniciou</label>
                            <input name="drogasdata" type="date" class="form-control"
                              onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_droga_data')"
                              value="<?php echo $resultado['mul_droga_data']; ?>">
                          </div>


                        </div>
                        <div class="col-sm-6">

                          <div class="form-group">
                            <label for="mul_droga_qual">Qual a substância</label>
                            <input name="qualdrogas" type="text" class="form-control"
                              onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_droga_qual')"
                              value="<?php echo $resultado['mul_droga_qual']; ?>">
                          </div>

                        </div>
                      </div>

                      <div class="checkbox">
                        <label>
                          <input id="doencasaude" value="sim" type="checkbox" name="opt1" value="1"
                            onchange="submetetruefalse(this.checked, <?php echo $codigo ?>, 'mul_doenca')" <?php if ($resultado['mul_doenca'] == '1')
                                 echo "checked='checked'"; ?>> Tem alguma doença ou
                          problema de saúde?
                        </label>
                      </div>
                      <div class="form-group" data-visibility-target="doencasaude" data-visibility-value="sim">
                        <label for="mul_doenca_qual">Se sim, qual</label>
                        <input name="doencaqual" type="text" class="form-control"
                          onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_doenca_qual')"
                          value="<?php echo $resultado['mul_doenca_qual']; ?>">
                      </div>

                      <div class="checkbox">
                        <label>
                          <input id="dificuldadesaude" value="sim" type="checkbox" name="opt1" value="1"
                            onchange="submetetruefalse(this.checked, <?php echo $codigo ?>, 'mul_dificuldade_tratamento')"
                            <?php if ($resultado['mul_dificuldade_tratamento'] == '1')
                              echo "checked='checked'"; ?>>
                          Existem dificuldades da família para realizar tratamento / acompanhamento de saúde
                        </label>
                      </div>

                      <div class="form-group" data-visibility-target="dificuldadesaude" data-visibility-value="sim">
                        <label for="mul_dificuldade_tratamento_qual">Se sim, quais são estas dificuldades?</label>
                        <input name="dificuldadesdetratamento" type="text" class="form-control"
                          onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_dificuldade_tratamento_qual')"
                          value="<?php echo $resultado['mul_dificuldade_tratamento_qual']; ?>">
                      </div>
                      <div class="checkbox">
                        <label>
                          <input id="acompanhamentosaude" type="checkbox" name="opt1" value="1"
                            onchange="submetetruefalse(this.checked, <?php echo $codigo ?>, 'mul_acompanha_saude')"
                            <?php if ($resultado['mul_acompanha_saude'] == '1')
                              echo "checked='checked'"; ?>> Faz
                          acompanhamento em alguma unidade de Saúde?
                        </label>
                      </div>

                      <div class="form-group" data-visibility-target="acompanhamentosaude" data-visibility-value="sim">
                        <label for="mul_acompanhamento_saude_local">Se sim, em qual?</label>
                        <input name="unidadedesaude" type="text" class="form-control"
                          onchange="submetevalor(this.value, <?php echo $codigo ?>, 'mul_acompanhamento_saude_local')"
                          value="<?php echo $resultado['mul_acompanhamento_saude_local']; ?>">
                      </div>



                      <!--<div class="card-footer row justify-content-md-center justify-content-sm-center justify-content-center">-->
                      <!--  <button type="buton" class="btn btn-success" data-toggle2="modal" data-target2="#modal-adversa">Salvar alterações</button>-->
                      <!--</div>-->


                    </div>
                  </div>
                </form>
              </div>
              <div class="tab-pane" id="permissao">
                <div class="card">
                  <div class="card-header">
                    <label for="filter-tecnicos" class="form-label">Filtrar Técnicos:</label>
                    <input type="text" id="filter-tecnicos" class="form-control mb-3"
                      placeholder="Digite o nome do técnico para filtrar" onkeyup="filtrarTecnicos()">
                  </div>
                  <div class="card-body">
                  <div class="card-header">
                  <label for="tecnicos" class="form-label">Selecione Técnicos:</label>
                  </div>
                    <div class="row">
                      <div class="col-12">
                        <form method="POST" action="salvar_atendimento.php">
                          <select name="tecnicos[]" id="tecnicos" class="form-select col-12" aria-label="Seleção de técnicos"
                            multiple>
                            <?php
                            // Recuperar o código da mulher
                            $codigo = $_GET['codigo'];

                            // Recuperar o ID do criador da mulher
                            $criadorQuery = $MySQLi->query("
                SELECT mul_tec_codigo 
                FROM tb_mulheres 
                WHERE mul_codigo = $codigo
              ");
                            $criador = $criadorQuery->fetch_assoc()['mul_tec_codigo'];

                            // Consultar os técnicos já associados ao atendimento da mulher
                            $tecnicosAssociados = $MySQLi->query("
                SELECT tec_codigo
                FROM tb_tecnicos_mulheres
                WHERE mul_codigo = $codigo
              ");

                            // Criar um array com os IDs dos técnicos já associados
                            $tecnicosAssociadosArray = [];
                            while ($tec = $tecnicosAssociados->fetch_assoc()) {
                              $tecnicosAssociadosArray[] = $tec['tec_codigo'];
                            }

                            // Recuperar os técnicos disponíveis para serem adicionados
                            $tecnicos = $MySQLi->query("SELECT tec_codigo, tec_nome FROM tb_tecnicos");

                            // Iterar sobre os técnicos e exibir apenas os que ainda não foram associados e não são o criador
                            while ($tecnico = $tecnicos->fetch_assoc()) {
                              if (!in_array($tecnico['tec_codigo'], $tecnicosAssociadosArray) && $tecnico['tec_codigo'] != $criador) {
                                echo "<option value='{$tecnico['tec_codigo']}'>{$tecnico['tec_nome']}</option>";
                              }
                            }
                            ?>
                          </select>
                          <input type="hidden" name="codigo" value="<?= $codigo ?>"> <!-- Código da mulher -->
                          <button type="submit" class="btn btn-primary mt-3 col-12">Salvar</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <script>
                // Função para filtrar técnicos pelo nome
                function filtrarTecnicos() {
                  const input = document.getElementById('filter-tecnicos').value.toLowerCase();
                  const options = document.getElementById('tecnicos').options;

                  for (let i = 0; i < options.length; i++) {
                    const tecnico = options[i].text.toLowerCase();
                    options[i].style.display = tecnico.includes(input) ? '' : 'none';
                  }
                }
              </script>

              <div class="tab-pane" id="anexos">

                <hr>
                <h2>Upload de Documentos</h2>
                <form action="?codigo=<?= $codigo ?>" method="post" enctype="multipart/form-data">
                  <label for="file">Selecione o Documento:</label>
                  <input type="file" name="file" required><br><br>

                  <label for="novo_nome">Novo Nome (opcional):</label>
                  <input type="text" name="novo_nome" placeholder="Digite um novo nome para o arquivo"><br><br>

                  <button type="submit" class="btn btn-success" name="upload">Upload</button>
                </form>

                <h2>Documentos Anexados</h2>
                <table border="1" cellpadding="8" cellspacing="0">
                  <tr>
                    <th>ID</th>
                    <th>Nome do Documento</th>
                    <th>Data de Upload</th>
                    <th>Ações</th>
                  </tr>
                  <?php
                  $documentos = buscarDocumentos($MySQLi, $codigo);
                  while ($row = $documentos->fetch_assoc()) {
                    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['nome_arquivo']}</td>
            <td>{$row['data_upload']}</td>
            <td><a href='{$row['caminho_arquivo']}' download>Baixar</a></td>
        </tr>";
                  }
                  ?>
                </table>

              </div>
              <div class="<?php if ($aba == "atendimento")
                echo "active" ?> tab-pane" id="atendimento">
                <?php if (@$msg == 4)
                echo
                  "<div id='alerta' class='alert alert-success' role='alert'>
                            Atendimento adicionado!
                        </div>";
              ?>
                <div class="card card-secondary">
                  <div class="card-header">
                    <h3 class="card-title">
                      <i class="fas"></i>
                      Atendimentos
                    </h3>
                  </div>
                  <div class="card-body">

                    <table class="table table-hover table-bordered" role="grid" aria-describedby="example1_info">
                      <thead>
                        <tr>
                          <th>Técnicos</th>
                          <th>Data</th>
                          <th>Tipo</th>
                          <th>Ação</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php while ($resultado8 = $consulta8->fetch_assoc()) { ?>
                          <tr>
                            <td class="align-middle"><?php echo $resultado8['tec1'];
                            if ($resultado8['tec2'] != null)
                              echo " e " . $resultado8['tec2']; ?></td>
                            <td class="align-middle"><?php echo $resultado8['data']; ?></td>
                            <td class="align-middle"><?php echo $resultado8['tipo']; ?></td>
                            <td class="align-middle"><a
                                href="?codigo=<?php echo $codigo; ?>&atendimento=<?php echo $resultado8['ate_codigo']; ?>"><button
                                  type="button" class="btn btn-block bg-gradient-primary btn-xs"><i
                                    class="fas fa-search"></i> ver</button></a></td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>

                  </div>

                  <div class="card-footer justify-content-md-center text-center">
                    <button type="submit" class="btn btn-success m-2 " data-toggle="modal"
                      data-target="#modal-atendimento">Iniciar novo atendimento</button>
                    <a href="abrigamento-novo.php?mulher=<?php echo $codigo ?>"><button type="submit"
                        class="btn btn-secondary m-2 ">Iniciar abrigamento</button></a>
                  </div>

                </div>


                <?php if (isset($_GET['atendimento'])) { ?>


                  <div class="card card-danger card-outline">
                    <div class="card-header">
                      <h3 class="card-title">
                        <i class="fas"></i>
                        Detalhes do Atendimento
                      </h3>
                    </div>
                    <div class="card-body">

                      <form class="form-horizontal" method="POST" action="?">


                        <div class="row">
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label for="ate_data">Data do atendimento</label>
                              <input name="dataatendimento" type="datetime-local" class="form-control"
                                onblur="submetevaloratendimento(this.value, <?php echo $atendimento ?>, 'ate_data')"
                                value="<?php echo $resultado12['ate_data2']; ?>">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-sm-6">

                            <div class="form-group">
                              <label>Técnico 1</label>
                              <select name="tecnico1" class="custom-select"
                                onchange="submetevaloratendimento(this.value, <?php echo $atendimento; ?>, 'ate_tec_codigo1')">
                                <?php
                                $tec1 = $resultado12['ate_tec_codigo1'];
                                $consulta14 = $MySQLi->query("SELECT * FROM tb_tecnicos where tec_ativo = 1 or tec_codigo = $tec1");
                                while ($resultado14 = $consulta14->fetch_assoc()) { ?>
                                  <option value="<?php echo $resultado14['tec_codigo']; ?>" <?php
                                     if ($resultado14['tec_codigo'] == $tec1)
                                       echo "selected='selected'";
                                     ?>>
                                    <?php echo $resultado14['tec_apelido']; ?>
                                  </option>
                                <?php } ?>
                              </select>
                            </div>

                          </div>
                          <div class="col-sm-6">
                            <div class="form-group">
                              <label>Técnico 2</label>
                              <select name="tecnico2" class="custom-select"
                                onchange="submetevaloratendimento(this.value, <?php echo $atendimento; ?>, 'ate_tec_codigo2')">
                                <option value="0">-</option>
                                <?php
                                $tec2 = $resultado12['ate_tec_codigo2'];
                                if ($tec2 == "")
                                  $tec2 = $_SESSION['id'];
                                $consulta13 = $MySQLi->query("SELECT * FROM tb_tecnicos where tec_ativo = 1 or tec_codigo = $tec2");
                                while ($resultado13 = $consulta13->fetch_assoc()) { ?>
                                  <option value="<?php echo $resultado13['tec_codigo']; ?>" <?php
                                     if ($resultado13['tec_codigo'] == $resultado12['ate_tec_codigo2'])
                                       echo "selected='selected'";
                                     ?>><?php echo $resultado13['tec_apelido']; ?></option>
                                <?php } ?>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="form-group">
                          <label for="ate_resumo">Resumo simplificado do motivo do atendimento (para timeline)</label>
                          <input name="resumo" type="text" class="form-control"
                            onchange="submetevaloratendimento(this.value, <?php echo $atendimento ?>, 'ate_resumo')"
                            value="<?php echo $resultado12['ate_resumo']; ?>">
                        </div>
                        <p><b>Relatório:</b></p>
                        <div class="card-body pad">
                          <div class="mb-3">
                            <textarea class="textarea" style="width: 100%; height: 200px; font-size: 14px;
                            line-height: 18px; border: 1px solid #dddddd; padding: 10px;" id="mytextarea"
                              name="relatorio"
                              onblur="alert(this.value);submetevaloratendimento(tinyMCE.activeEditor.getContent(), <?php echo $atendimento ?>, 'ate_relatorio')"><?php echo $resultado12['ate_relatorio']; ?></textarea>
                          </div>
                        </div>


                    </div>

                    <div class="card-footer justify-content-md-center text-center">
                      <button type="button" class="btn btn-success"
                        onclick="submetevaloratendimento(tinyMCE.activeEditor.getContent(), <?php echo $atendimento ?>, 'ate_relatorio')">Salvar</button>
                    </div>
                    </form>

                    <!-- /.tab-pane -->
                  </div>

                <?php } ?>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
</section>


<div class="modal fade" id="modal-adversa">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Adicionar Parte adversa</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form role="form" method="POST" action="?codigo=<?php echo $codigo ?>">
        <div class="modal-body">
          <input type="hidden" name="mulher" value="<?php echo $codigo ?>">
          <input type="text" name="adversa" class="form-control"
            placeholder="Digite o nome da parte adversa para iniciar" required="required">
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Adicionar</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<div class="modal fade" id="modal-social">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Adicionar Pessoa</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form role="form" method="POST" action="?codigo=<?php echo $codigo ?>">
        <div class="modal-body">
          <input type="hidden" name="mulher" value="<?php echo $codigo ?>">
          <input type="text" name="pessoa" class="form-control" placeholder="Digite o nome da pessoa para iniciar"
            required="required">
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Adicionar</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<div class="modal fade" id="modal-atendimento">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Adicionar Atendimento</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form role="form" method="POST" action="?codigo=<?php echo $codigo ?>">
        <div class="modal-body">
          <input type="hidden" name="atendimento" value="1">
          Clique em adicionar para registrar um atendimento técnico.
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Adicionar</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="modal-foto">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Foto da mulher</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <img class="img-fluid" src="<?php echo 'imagens/mulheres/' . $resultado['mul_foto'] . '.jpg?' . time() ?>"
          width="250">
      </div>
      <form role="form" method="POST" action="?codigo=<?php echo $codigo ?>" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="mulher" value="<?php echo $codigo ?>">
          <input type="file" name="foto" accept="image/jpeg" class="form-control" placeholder="Sua foto de perfil">
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar Envio</button>
          <button type="button" onclick="window.location.href = '?codigo=<?php echo $codigo ?>&excluir=1'"
            class="btn btn-danger">Excluir Foto</button>
          <button type="submit" class="btn btn-primary">Enviar Foto</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>



<?php
include("design2.php");
?>
<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/toastr/toastr.min.js"></script>
<script src="plugins/conditional/conditional-visibility.min.js"></script>
<script>
  $(function () {

    $('#example2').DataTable({
      "paging": false,
      "lengthChange": true,
      "searching": false,
      "ordering": true,
      "info": false,
      "autoWidth": false,
      "responsive": true,
    });
  });


</script>