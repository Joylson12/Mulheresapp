<?php
session_start();
require_once('includes/config.php');

// Verifique se $dbVisTables está definido
if (!isset($dbVisTables)) {
    die("A variável dbVisTables não está definida. Verifique o arquivo config.php.");
}

// Exploda dbVisTables em um array
$visTables = explode(",", $dbVisTables);
if (count($visTables) == 1) {
    if ($visTables[0] != "") {
        $_SESSION['selectedTables'] = "`" . $visTables[0] . "`";
        header("Location: selectFields.php");
        exit; // Sair após o cabeçalho para evitar execução adicional
    }
}

// Use a conexão mysqli já criada para mulheresapp_natal
$connDB = $mysqli_natal;

// Verifique a conexão
if ($connDB->connect_error) {
    die("Connection failed: " . $connDB->connect_error);
}

// Obtenha tabelas
$query_recGetTables = "SHOW TABLES";
$recGetTables = $connDB->query($query_recGetTables);

// Verifique se a consulta foi bem-sucedida
if (!$recGetTables) {
    die("Query failed: " . $connDB->error);
}

// Obtenha a primeira linha dos resultados
$row_recGetTables = $recGetTables->fetch_array();
$totalRows_recGetTables = $recGetTables->num_rows;

// Código restante...
$design_titulo = "Relatórios";
$design_ativo = "r3";
$design_migalha1_texto = "Relatórios";
$design_migalha1_link = "index.php";
$design_migalha2_texto = "Editar: Parte 1";
$design_migalha2_link = "";

// Redefinição de sessão ao clicar em "Reiniciar como novo relatório"
if (isset($_POST['cmdNew'])) {
    // Limpe as variáveis de sessão relacionadas ao relatório
    unset($_SESSION['selectedFields']);
    unset($_SESSION['txtReportName']);
    unset($_SESSION['appliedConditions']);
    unset($_SESSION['lstSortName']);
    unset($_SESSION['lstSortOrder']);
    unset($_SESSION['txtRecPerPage']);
    unset($_SESSION['status']);

    // Reinicie as variáveis se necessário
    $_SESSION['selectedTables'] = ""; // Mantém a variável de tabelas selecionadas vazia para permitir nova seleção
}

if (isset($_POST['cmdNext'])) {
    // Salva as tabelas selecionadas na sessão ao clicar em "Avançar"
    $_SESSION['selectedTables'] = $_POST['selectedTables'];
    header("Location: selectFields.php"); // Redireciona para a página selectFields.php
    exit; // Sair após o cabeçalho para evitar execução adicional
}

include("design1.php");
?>

<script language="javascript" type="text/javascript">
function cmdSelectTables_onclick() {
    var lstAllTables = document.getElementById('lstAllTables');
    var lstTables = document.getElementById('lstTables');
    
    // Move todos os itens selecionados
    for (var i = lstAllTables.options.length - 1; i >= 0; i--) {
        if (lstAllTables.options[i].selected) {
            var option = lstAllTables.options[i];
            lstTables.appendChild(option); // Move a opção
        }
    }
    updateHiddenField();
}

function cmdRemoveTables_onclick() {
    var lstTables = document.getElementById('lstTables');
    var lstAllTables = document.getElementById('lstAllTables');
    
    // Move todos os itens selecionados de volta
    for (var i = lstTables.options.length - 1; i >= 0; i--) {
        if (lstTables.options[i].selected) {
            var option = lstTables.options[i];
            lstAllTables.appendChild(option); // Move a opção de volta
        }
    }
    updateHiddenField();
}

function updateHiddenField() {
    var lstTables = document.getElementById('lstTables');
    var selectedTables = [];
    
    for (var i = 0; i < lstTables.options.length; i++) {
        selectedTables.push(lstTables.options[i].value);
    }
    
    // Atualiza o campo oculto
    document.getElementById('selectedTables').value = selectedTables.join('~');
    
    // Habilita o botão Avançar se houver tabelas selecionadas
    document.getElementById('cmdNext').disabled = selectedTables.length === 0;
}

</script>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Selecione a(s) tabela(s) que compõem este relatório</h3>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['txtReportName']) && $_SESSION['txtReportName'] != "") {
                    echo "<h2>Edição: " . $_SESSION['txtReportName'] . "</h2>";
                } else {
                    echo "<h2>Novo Relatório</h2>";
                } ?>

                <div class="row">
                    <div class="col-md-5 col-12">
                        Lista de tabelas disponíveis
                        <select name="lstAllTables" size="10" multiple id="lstAllTables" class='form-control'>
                            <?php
                            do {
                                if ($dbVisTables != "") {
                                    $visTables = explode(",", $dbVisTables);
                                    for ($x = 0; $x < count($visTables); $x++) {
                                        if ($row_recGetTables[0] == trim($visTables[$x])) {
                                            ?>
                                            <option value="<?php echo "`" . $row_recGetTables[0] . "`"; ?>"><?php echo $row_recGetTables[0]; ?></option>
                                            <?php
                                        }
                                    }
                                } else {
                                    ?>
                                    <option value="<?php echo "`" . $row_recGetTables[0] . "`"; ?>"><?php echo $row_recGetTables[0]; ?></option>
                                    <?php
                                }
                            } while ($row_recGetTables = mysqli_fetch_array($recGetTables));
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2 col-12 card-body text-center">
                        <a href="javascript:void(0);" class="btn btn-sm btn-primary m-2" onclick="cmdSelectTables_onclick();"><i class="fas fa-arrow-right"></i></a><br>
                        <a href="javascript:void(0);" class="btn btn-sm btn-primary" onclick="cmdRemoveTables_onclick();"><i class="fas fa-arrow-left"></i></a>
                    </div>

                    <div class="col-md-5 col-12">
                        Tabelas selecionadas
                        <select name="lstTables" size="10" multiple id="lstTables" class='form-control' onchange="updateHiddenField();">
                            <?php
                            $tmpTables = explode("~", $_SESSION['selectedTables']);
                            for ($x = 0; $x < count($tmpTables); $x++) {
                                if ($tmpTables[$x] != "") {
                                    ?>
                                    <option value="<?php echo $tmpTables[$x]; ?>"><?php echo $tmpTables[$x]; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <br>

                <form action="" method="post" name="frmTables" id="frmTables">
                    <div class="margin">
                        <button name="cmdNew" type="submit" class="btn btn-info" id="cmdNew"><i class="fas fa-file"></i> Reiniciar como novo relatório</button>
                        <button name="cmdBack" type="button" class="btn btn-success m-2" id="cmdBack" onclick="jumpURL('index.php');"><i class="fas fa-arrow-left"></i> Voltar</button>
                        <button name="cmdNext" type="submit" class="btn btn-success" id="cmdNext" disabled>Avançar <i class="fas fa-arrow-right"></i></button>
                    </div>
                    <input name="selectedTables" type="hidden" id="selectedTables" value="<?php echo ($_SESSION['selectedTables']); ?>">
                </form>
            </div>
        </div>
    </div>
</section>


<style>
  /* Estilo para garantir que o conteúdo principal não fique atrás da sidebar */
  .content {
    margin-left: 250px;
    /* Ajuste de acordo com a largura da sidebar */
    padding: 20px;
    /* Espaçamento interno para o conteúdo */
  }

  /* Sidebar fixa */
  .main-sidebar {
    position: fixed;
    /* Manter a sidebar fixa */
    height: 100%;
    /* Ocupa toda a altura da página */
    overflow-y: auto;
    /* Rolagem vertical se necessário */
  }

  /* Wrapper para flexbox */
  .wrapper {
    display: flex;
    /* Flexbox para melhor layout */
  }

  /* Estilos responsivos */
  @media (max-width: 768px) {
    .content {
      margin-left: 0;
      /* Remove a margem da sidebar em telas menores */
      padding: 10px;
      /* Ajusta o espaçamento interno */
    }

    .main-sidebar {
      position: relative;
      /* Altera a posição para se adaptar ao layout */
      height: auto;
      /* Ajusta a altura da sidebar */
    }

    .row {
      flex-direction: column;
      /* Altera a direção para coluna em telas menores */
    }
  }
</style>

<?php
include('design2.php');
mysqli_free_result($recGetTables);
?>
