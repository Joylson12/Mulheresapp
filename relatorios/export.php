<?php
session_start();
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=" . str_replace (" ","",$_SESSION['txtReportName']) . ".xls");

require_once('includes/config.php');

$currentPage = $_SERVER["PHP_SELF"];
$maxRows_recSQL = "18446744073709551615";
$pageNum_recSQL = 0;
$startRow_recSQL = $pageNum_recSQL * $maxRows_recSQL;

// Use a conexão correta para o banco de dados mulheresapp_natal
$database_connDB = "mulheresapp_natal"; // Defina o nome do seu banco de dados aqui
$connDB = new mysqli($endereco, $usuario, $senha, $database_connDB);

if ($connDB->connect_error) {
    die("Conexão falhou: " . $connDB->connect_error);
}

$query_recSQL = $_SESSION["tmpSQL"];
$query_limit_recSQL = sprintf("%s LIMIT %d, %d", $query_recSQL, $startRow_recSQL, $maxRows_recSQL);
$recSQL = $connDB->query($query_limit_recSQL);

if (!$recSQL) {
    die("Erro na consulta: " . $connDB->error);
}

$column_count = $recSQL->field_count;

if (isset($_GET['totalRows_recSQL'])) {
    $totalRows_recSQL = $_GET['totalRows_recSQL'];
} else {
    $all_recSQL = $connDB->query($query_recSQL);
    $totalRows_recSQL = $all_recSQL->num_rows;
}
$totalPages_recSQL = ceil($totalRows_recSQL/$maxRows_recSQL)-1;

$queryString_recSQL = "";
if (!empty($_SERVER['QUERY_STRING'])) {
    $params = explode("&", $_SERVER['QUERY_STRING']);
    $newParams = array();
    foreach ($params as $param) {
        if (stristr($param, "pageNum_recSQL") == false && 
            stristr($param, "totalRows_recSQL") == false) {
            array_push($newParams, $param);
        }
    }
    if (count($newParams) != 0) {
        $queryString_recSQL = "&" . htmlentities(implode("&", $newParams));
    }
}

$queryString_recSQL = sprintf("&totalRows_recSQL=%d%s", $totalRows_recSQL, $queryString_recSQL);

print("<TABLE border='1' cellspacing='0' cellpading='0'> \n");
print("<TR ALIGN=LEFT VALIGN=TOP>");
for ($column_num = 0; $column_num < $column_count; $column_num++) {
    $field_info = $recSQL->fetch_field_direct($column_num);
    $field_name = $field_info->name;
    print(mb_convert_encoding("<TD bgcolor='#CCCCCC'><b>$field_name</b></TD>", 'iso-8859-1', 'utf-8'));
}
print("</TR>\n");

while ($row = $recSQL->fetch_row()) {
    print("<TR>");
    for ($column_num = 0; $column_num < $column_count; $column_num++) {
        print("<TD>");
        if ($row[$column_num] != "") {
            print(mb_convert_encoding($row[$column_num], 'iso-8859-1', 'utf-8'));
        } else {
            print("&nbsp;");
        }
        print("</TD>\n");
    }
    print("</TR>\n");
}
print("</TABLE>");

$connDB->close();
?>
