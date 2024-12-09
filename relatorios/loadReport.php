<?php 
session_start(); 
require_once('includes/config.php'); 

// Defina o nome do banco de dados relatorios
$database_connSave = "mulheresapp_relatorios"; 

$colname_recLoad = "-1";
if (isset($_GET['id'])) {
    $colname_recLoad = addslashes($_GET['id']);
}

// Conexão com o banco de dados relatorios
$connSave = new mysqli($endereco, $usuario, $senha, $database_connSave);
if ($connSave->connect_error) {
    die("Conexão falhou: " . $connSave->connect_error);
}

// Prepare a consulta
$query_recLoad = sprintf("SELECT * FROM tblreports WHERE id = '%s'", $connSave->real_escape_string($colname_recLoad));
$recLoad = $connSave->query($query_recLoad);

if (!$recLoad) {
    die("Erro na consulta: " . $connSave->error);
}

$row_recLoad = $recLoad->fetch_assoc();

// Armazena os dados na sessão
$_SESSION['appliedConditions'] = $row_recLoad['appliedConditions'];
$_SESSION['txtReportName'] = $row_recLoad['txtReportName'];
$_SESSION['lstSortName'] = $row_recLoad['lstSortName'];
$_SESSION['lstSortOrder'] = $row_recLoad['lstSortOrder'];
$_SESSION['txtRecPerPage'] = $row_recLoad['txtRecPerPage'];
$_SESSION['selectedFields'] = $row_recLoad['selectedFields'];
$_SESSION['selectedTables'] = $row_recLoad['selectedTables'];

// Redireciona para generateSQL.php
header("Location: generateSQL.php");

// Libera o resultado e fecha a conexão
$recLoad->free();
$connSave->close();
?>
