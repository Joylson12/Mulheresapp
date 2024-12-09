<?php 
require_once('includes/config.php'); 

// Variáveis para paginação
$currentPage = $_SERVER["PHP_SELF"];
$maxRows_recReports = 10;
$pageNum_recReports = 0;
if (isset($_GET['pageNum_recReports'])) {
  $pageNum_recReports = $_GET['pageNum_recReports'];
}
$startRow_recReports = $pageNum_recReports * $maxRows_recReports;

// Consultando o banco de dados
$query_recReports = "SELECT * FROM tblreports WHERE status = 0 ORDER BY id DESC";
$query_limit_recReports = sprintf("%s LIMIT %d, %d", $query_recReports, $startRow_recReports, $maxRows_recReports);
$recReports = $MySQLi->query($query_limit_recReports);

if (!$recReports) {
    die("Erro na consulta: " . $MySQLi->error);
}

// Verificando o total de registros
if (isset($_GET['totalRows_recReports'])) {
  $totalRows_recReports = $_GET['totalRows_recReports'];
} else {
  $all_recReports = $MySQLi->query($query_recReports);
  $totalRows_recReports = $all_recReports->num_rows;
}

$totalPages_recReports = ceil($totalRows_recReports / $maxRows_recReports) - 1;

$queryString_recReports = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_recReports") == false && 
        stristr($param, "totalRows_recReports") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_recReports = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_recReports = sprintf("&totalRows_recReports=%d%s", $totalRows_recReports, $queryString_recReports);

$design_titulo = "Relatórios do Banco de Dados";
$design_ativo = "r2"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Relatórios";
$design_migalha1_link = "";
$design_migalha2_texto = "";
$design_migalha2_link = "";

include("design1.php");
?>

<script language="javascript" type="text/javascript">
function cmdDelete_onClick(recID) {
  var tmpVal = confirm("Please Confirm Action");
  if (tmpVal == true) {
    window.open("delReport.php?id=" + recID, "_self");
  }
}
</script>
<div class="wrapper">
    <!-- Main content -->
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <blockquote>
                            Siga as instruções abaixo para obter melhores resultados.
                        </blockquote>
                        <h3>Como usar relatórios</h3>
                        <ul>
                            <li>Escolha, na lista da esquerda, algum dos relatórios já cadastrados.</li>
                            <li>É possível buscar qualquer valor na tabela, usando o campo <b>Pesquisar</b> (à direita, acima da tabela).</li>
                            <li>Se clicar no nome da coluna, ela será ordenada automaticamente &#8593;&#8595;.</li>
                            <li>Caso a tabela tenha muitas colunas, será criado um ícone azul (+) dentro da primeira coluna, para expandir os resultados.</li>
                            <li>Caso deseje, use o botão <b><i class="nav-icon fas fa-file-excel"></i> Exportar para Excel</b>, no fim da tabela, para salvar os resultados.</li>
                        </ul>
                        <h3>Como editar ou criar um novo relatório?</h3>
                        <ul>
                            <li>Ao clicar em <b>configurar relatório</b>, o sistema exibe as opções de personalizar o último relatório utilizado.</li>
                            <li>Para criar um novo relatório, clique em <b>Configurar Relatório</b> e depois em <b>Reiniciar como novo formulário</b>.</li>
                            <li>Estes relatórios acessam todo o banco de dados do sistema.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>



<?php
include("design2.php");

// Liberando os resultados
$recReports->free();
?>
