<?php
include("config.php");
include("acesso.php");
include("permisaoAdm.php");

// Variáveis para definição antes de incluir o design1.php
$design_titulo = "Chamados de Suporte ao Sistema";
$design_ativo = "m11"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Suporte";
$design_migalha1_link = "";
$design_migalha2_texto = "";
$design_migalha2_link = "";

include("design1.php");

// Inicializar a variável $antigos
$antigos = 0;

// Consultar chamados com base no parâmetro 'antigos'
if (isset($_GET['antigos']) && $_GET['antigos'] == 1) {
  $consulta = $MySQLi->query("SELECT * FROM tb_chamados
                        JOIN tb_tecnicos ON cha_tec_codigo = tec_codigo
                        ORDER BY cha_data_pedido DESC");
  $antigos = 1;
} else {
  $consulta = $MySQLi->query("SELECT * FROM tb_chamados
                        JOIN tb_tecnicos ON cha_tec_codigo = tec_codigo
                        WHERE cha_resposta IS NULL 
                           OR cha_data_pedido BETWEEN DATE_ADD(CURRENT_DATE(), INTERVAL -15 DAY) AND CURRENT_DATE()
                        ORDER BY cha_data_pedido DESC");
}

// Processar envio de novo chamado
if (isset($_POST['chamado'])) {
  $chamado = trim($_POST['chamado']);
  if ($chamado == '') {
    header("Location: ?msg=1");
    exit();
  } else {
    // Usar consultas preparadas para evitar SQL Injection
    $stmt = $MySQLi->prepare("INSERT INTO tb_chamados (cha_pedido, cha_tec_codigo) VALUES (?, ?)");
    if ($stmt) {
      $stmt->bind_param("si", $chamado, $id); // Supondo que $id é o ID do técnico logado
      if ($stmt->execute()) {
        header("Location: ?msg=2");
        exit();
      } else {
        echo "Erro ao cadastrar o chamado: " . $stmt->error;
      }
      $stmt->close();
    } else {
      echo "Erro na preparação da consulta: " . $MySQLi->error;
    }
  }
}

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<!-- Inclua o TinyMCE via CDN -->
<script src="https://cdn.tiny.cloud/1/k7vhbf0ybiy0bsqxhlfwwfww6zcohn8dz5eo1rg71vgdzsx3/tinymce/7/tinymce.min.js"
  referrerpolicy="origin"></script>

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
    <?php
    if (@$msg == 2) {
      echo "<div id='alerta' class='alert alert-success' role='alert'>
                  Chamado cadastrado com sucesso!
                </div>";
    }
    ?>
    <div class="card card-primary card-outline">
      <div class="card-body">

        <blockquote>
          Este é o contato com a equipe técnica do sistema. Use este espaço para registrar problemas técnicos, falhas,
          recursos em falta, ou mesmo sugestões de recursos que ajudariam no seu dia a dia de uso do sistema.
          Caso deseje relatar um erro, informe o máximo de detalhes que seja possível: em que página isto ocorreu, o que
          você fez e o que estava tentando fazer, para que a equipe possa reproduzir o mesmo erro nos testes.
        </blockquote>

        <div class="form-group">
          <div class="custom-control custom-switch">
            <input <?php if ($antigos == 1)
              echo 'checked'; ?> type="checkbox" class="custom-control-input"
              id="customSwitch1"
              onclick="this.checked ? window.location.href = '?antigos=1' : window.location.href = '?'">
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
                    <?php
                    while ($resultado = $consulta->fetch_assoc()) { ?>
                      <tr class="text-justify">
                        <td>
                          <div class="text-center">
                            <div class="image">
                              <img
                                src="<?php echo 'imagens/tecnicos/' . htmlspecialchars($resultado['tec_codigo']) . '.jpg'; ?>"
                                class="img-circle elevation-2" width="30" alt="Foto do Técnico"><br>
                            </div>
                            <div class="info">
                              <?php echo htmlspecialchars($resultado['tec_apelido']); ?>
                            </div>
                          </div>
                        </td>
                        <td>
                          <?php echo htmlspecialchars(data($resultado['cha_data_pedido'])); ?> <br>
                          <?php echo nl2br(strip_tags(html_entity_decode(htmlspecialchars($resultado['cha_pedido'])))); ?>
                        </td>
                        <td>
                          <span class="text-green">
                            <?php
                            if (!empty($resultado['cha_data_resposta'])) {
                              echo htmlspecialchars(data($resultado['cha_data_resposta'])) . " <br> ";
                            }
                            ?>
                            <?php echo nl2br(strip_tags(html_entity_decode(htmlspecialchars($resultado['cha_resposta'])))); ?>
                          </span>
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

        <div class="card card-secondary">
          <div class="card-header">
            <h3 class="card-title">Adicionar novo chamado de Suporte</h3>
          </div>

          <div class="card-body">

            <!-- Mensagem de erro para chamado vazio -->
            <?php
            if (@$msg == 1) {
              echo "<div id='alerta' class='alert alert-danger' role='alert'>
                    Não é possível enviar um chamado vazio!
                  </div>";
            }
            ?>

            <form role="form" method="POST" action="?" id="form">
              <p><b>Descreva o que deseja:</b></p>
              <div class="mb-3">
                <textarea name="chamado" class="textarea" style="width: 100%; height: 200px; font-size: 14px; 
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

<!-- Scripts do DataTables -->
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
    $(".dataTable").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
  });
</script>

<?php
include("design2.php");
?>