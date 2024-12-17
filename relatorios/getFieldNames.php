<?php
require_once('includes/config.php');

// Verifica se a tabela foi passada
if (!isset($_POST["tableName"])) {
    die("Nome da tabela não fornecido.");
}

$tableName = $_POST["tableName"];
$tableNameClean = trim($tableName, '`'); // Remove acentos graves

if (empty($tableNameClean)) {
    die("Nome da tabela está vazio.");
}

// Verifica as tabelas existentes
$tabelas = $mysqli_natal->query("SHOW TABLES");
$tabelasExistentes = [];
while ($row = $tabelas->fetch_array(MYSQLI_NUM)) {
    $tabelasExistentes[] = $row[0]; // Adiciona os nomes das tabelas ao array
}


if (!in_array($tableNameClean, $tabelasExistentes)) {
    die("Tabela não encontrada: " . htmlspecialchars($tableNameClean));
}

// Usa a conexão mysqli para obter as colunas
$query_recGetFields = "SHOW COLUMNS FROM " . $mysqli_natal->real_escape_string($tableNameClean);
$recGetFields = $mysqli_natal->query($query_recGetFields);

if (!$recGetFields) {
    die("Erro na consulta: " . $mysqli_natal->error);
}

// Gera o HTML das colunas
echo '<select name="lstAllFields" size="10" multiple id="lstAllFields" class="form-control">';
while ($row_recGetFields = $recGetFields->fetch_array(MYSQLI_ASSOC)) {
    echo '<option value="' . htmlspecialchars($tableNameClean . ".`" . $row_recGetFields['Field'] . "`") . '">';
    echo htmlspecialchars($row_recGetFields['Field']);
    echo '</option>';
}
echo '</select>';

echo '<a href="javascript:cmdSelectFields_onclick();" name="cmdSelectFields" type="button" id="cmdSelectFields" class="btn btn-block bg-gradient-primary">';
echo '<i class="fas fa-arrow-right"></i> Adicionar Coluna</a>';

$recGetFields->free(); // Libera o resultado
?>