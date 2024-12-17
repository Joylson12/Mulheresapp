<?php
include("config.php");
include("acesso.php");
// Variáveis para definição antes de incluir o design1.php class="nav-link active"

$design_titulo = "Atendimentos";
$design_ativo = "m3"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Atendimentos";
$design_migalha1_link = "";
$design_migalha2_texto = "";
$design_migalha2_link = "";

?>

<?php
include("design1.php");

if (isset($_POST['cpf'])) {
  $cpf = $_POST['cpf'];
  $consulta = $MySQLi->query("SELECT mul_codigo FROM tb_mulheres WHERE mul_cpf = '$cpf'");

  if ($resultado = $consulta->fetch_assoc()) {
    // CPF encontrado, define $mul_codigo
    $mul_codigo = $resultado['mul_codigo'];
  } else {
    // CPF não encontrado, insere um novo registro e obtém $mul_codigo
    $MySQLi->query("INSERT INTO tb_mulheres (mul_cpf) VALUES ('$cpf')");
    $mul_codigo = $MySQLi->insert_id;
  }

  // Cria um novo atendimento vinculado ao mul_codigo
  $MySQLi->query("INSERT INTO tb_atendimentos (ate_mul_codigo, ate_tec_codigo1, ate_data) VALUES ('$mul_codigo', '" . $_SESSION['id'] . "', NOW())");

  // Redireciona para a página de visualização
  header("Location: mulher-ver.php?codigo=$mul_codigo&msg=1");
  exit();
}

// Obter o primeiro e o último atendimento (data)
if (isset($mul_codigo)) {
  $consulta_datas = $MySQLi->query("SELECT MIN(ate_data) as data_cadastro, MAX(ate_data) as ultimo_atendimento 
                                      FROM tb_atendimentos 
                                      WHERE ate_mul_codigo = '$mul_codigo'");
  $datas = $consulta_datas->fetch_assoc();
  $data_cadastro = $datas['data_cadastro'];
  $ultimo_atendimento = $datas['ultimo_atendimento'];
}

// Consultas para exibir dados na página
$consulta3 = $MySQLi->query("SELECT mul_foto, mul_nome, mul_codigo, ate_data 
                             FROM tb_atendimentos 
                             JOIN tb_mulheres ON mul_codigo = ate_mul_codigo
                             ORDER BY ate_codigo DESC LIMIT 10");

$consulta4 = $MySQLi->query("SELECT mul_foto, mul_nome, mul_codigo, ate_data 
                             FROM tb_atendimentos 
                             JOIN tb_mulheres ON mul_codigo = ate_mul_codigo
                             WHERE ate_tec_codigo1 = " . $_SESSION['id'] . "
                             OR ate_tec_codigo2 = " . $_SESSION['id'] . "
                             ORDER BY ate_codigo DESC LIMIT 5");
?>
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
<script>
  $(function () {
    $('[data-mask]').inputmask();
  });
</script>

<!-- Main content -->
<section class="content">
  <div class="container-fluid">


    <div class="row">
      <div class="col-md-6">

        <div class="card card-primary card-outline">
          <div class="card-header">
            <h3 class="card-title">Novo Atendimento</h3>
          </div> <!-- /.card-body -->
          <div class="card-body">
            <div id='alerta' class='alert alert-danger' style="display: none" role='alert'>
              CPF inválido!
            </div>
            <form role="form" method="POST" action="?">
              <p><b>Insira o CPF da mulher para iniciar um novo atendimento</b></p>
              <div class="input-group mb-3">
                <input name="cpf" id="cpf" type="text" class="form-control rounded-0"
                  data-inputmask="'mask': ['999.999.999-99']" data-mask>
                <span class="input-group-append">
                  <button type="submit" onclick="return TestaCPF(document.getElementById('cpf').value)"
                    class="btn btn-primary">Iniciar</button>
                </span>
              </div>
            </form>
          </div><!-- /.card-body -->
        </div>

        <div class="card card-primary card-outline">
          <div class="card-header">
            <h3 class="card-title">Seus Atendimentos Recentes</h3>
          </div> <!-- /.card-body -->
          <div class="card-body">

            <table class="table table-hover text-nowrap">
              <thead>
                <tr>
                  <th>Foto</th>
                  <th>Nome</th>
                  <th>Data</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($resultado4 = $consulta4->fetch_assoc()) { ?>
                  <tr>
                    <td class="align-middle"><img class="img-circle elevation-2" alt="" width="40"
                        src="<?php echo 'imagens/mulheres/' . $resultado4['mul_foto'] . '.jpg' ?>"></td>
                    <td class="align-middle"><?php echo $resultado4['mul_nome'] ?></td>
                    <td class="align-middle">
                      <?php if (date('Y-m-d') == date("Y-m-d", strtotime($resultado4['ate_data'])))
                        echo date("H:i", strtotime($resultado4['ate_data']));
                      else
                        echo date("d/m", strtotime($resultado4['ate_data'])) ?>
                      </td>
                      <td class="align-middle"><a href="mulher-ver.php?codigo=<?php echo $resultado4['mul_codigo']; ?>"
                        class="btn btn-sm btn-primary btn-xs"><i class="fas fa-folder-open"></i> Ver</a></td>
                    </td>
                  </tr>
                <?php } ?>

              </tbody>
            </table>

          </div><!-- /.card-body -->
        </div>


      </div>
      <div class="col-md-6">

        <div class="card card-primary card-outline">
          <div class="card-header">
            <h3 class="card-title">Atendimentos Recentes</h3>
          </div> <!-- /.card-body -->
          <div class="card-body">

            <table class="table table-hover text-nowrap">
              <thead>
                <tr>
                  <th>Foto</th>
                  <th>Nome</th>
                  <th>Data</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($resultado3 = $consulta3->fetch_assoc()) { ?>
                  <tr>
                    <td class="align-middle"><img class="img-circle elevation-2" alt="" width="40"
                        src="<?php echo 'imagens/mulheres/' . $resultado3['mul_foto'] . '.jpg' ?>"></td>
                    <td class="align-middle"><?php echo $resultado3['mul_nome'] ?></td>
                    <td class="align-middle">
                      <?php if (date('Y-m-d') == date("Y-m-d", strtotime($resultado3['ate_data'])))
                        echo date("H:i", strtotime($resultado3['ate_data']));
                      else
                        echo date("d/m", strtotime($resultado3['ate_data'])) ?>
                      </td>
                      <td class="align-middle"><a href="mulher-ver.php?codigo=<?php echo $resultado3['mul_codigo']; ?>"
                        class="btn btn-sm btn-primary btn-xs"><i class="fas fa-folder-open"></i> Ver</a></td>
                  </tr>
                <?php } ?>

              </tbody>
            </table>

          </div><!-- /.card-body -->
        </div>

      </div>
    </div>

  </div><!-- /.container-fluid -->
</section>

<?php
include("design2.php");
?>

<script>
  function TestaCPF(strCPF) {
    strCPF = strCPF.replace(/[^0-9]/g, '');
    var Soma;
    var Resto;
    Soma = 0;
    if (strCPF == "00000000000" || strCPF == "11111111111" || strCPF == "22222222222" || strCPF == "33333333333" || strCPF == "44444444444" || strCPF == "55555555555" || strCPF == "66666666666"
      || strCPF == "77777777777" || strCPF == "88888888888" || strCPF == "99999999999") {
      document.getElementById('alerta').style.display = "block";
      return false;
    }

    for (i = 1; i <= 9; i++) Soma = Soma + parseInt(strCPF.substring(i - 1, i)) * (11 - i);
    Resto = (Soma * 10) % 11;

    if ((Resto == 10) || (Resto == 11)) Resto = 0;
    if (Resto != parseInt(strCPF.substring(9, 10))) {
      document.getElementById('alerta').style.display = "block";
      return false;
    }

    Soma = 0;
    for (i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i - 1, i)) * (12 - i);
    Resto = (Soma * 10) % 11;

    if ((Resto == 10) || (Resto == 11)) Resto = 0;
    if (Resto != parseInt(strCPF.substring(10, 11))) {
      document.getElementById('alerta').style.display = "block";
      return false;
    }
    return true;
  }
</script>

<!--<script src="plugins/jquery/jquery.min.js"></script>-->
<!-- InputMask -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
<script>
  $(function () {
    $('[data-mask]').inputmask();
  });
</script>