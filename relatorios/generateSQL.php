<?php
session_start();
require_once('includes/config.php');

function dmyError()
{
	$_SESSION["dmyError"] = "An error has occurred in generating the report. <br/> This is usually caused when a DB table required/used for this report has been deleted.";
	$_SESSION["dmyErrorUrl"] = "selectTables.php";
	print "<script language=\"JavaScript\">";
	print "window.location = 'genError.php' ";
	print "</script>";
}

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
{
	$theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

	switch ($theType) {
		case "text":
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;
		case "long":
		case "int":
			$theValue = ($theValue != "") ? intval($theValue) : "NULL";
			break;
		case "double":
			$theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
			break;
		case "date":
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;
		case "defined":
			$theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
			break;
	}
	return $theValue;
}

// Inicializa a sessão se não existir
if (!isset($_SESSION['txtReportName'])) {
	$_SESSION['txtReportName'] = "";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["txtReportName"])) {
	$_SESSION['appliedConditions'] = $_POST["appliedConditions"];
	$_SESSION['txtReportName'] = $_POST["txtReportName"];
	$_SESSION['lstSortName'] = $_POST["lstSortName"];
	$_SESSION['lstSortOrder'] = $_POST["lstSortOrder"];
	$_SESSION['txtRecPerPage'] = $_POST["txtRecPerPage"];
	$_SESSION['selectedFields'] = $_POST["selectedFields"];
	$_SESSION['selectedTables'] = $_POST["selectedTables"];

	if ($_POST["lstSave"] == 1) {
		$deleteSQL = "DELETE FROM tblreports WHERE txtReportName = '" . $_SESSION['txtReportName'] . "'";
		$mysqli_natal->query($deleteSQL);

		$insertSQL = sprintf(
			"INSERT INTO tblreports (appliedConditions, txtReportName, lstSortName, lstSortOrder, txtRecPerPage, selectedFields, selectedTables) VALUES (%s, %s, %s, %s, %s, %s, %s)",
			GetSQLValueString($_SESSION['appliedConditions'], "text"),
			GetSQLValueString($_SESSION['txtReportName'], "text"),
			GetSQLValueString($_SESSION['lstSortName'], "text"),
			GetSQLValueString($_SESSION['lstSortOrder'], "text"),
			GetSQLValueString($_SESSION['txtRecPerPage'], "text"),
			GetSQLValueString($_SESSION['selectedFields'], "text"),
			GetSQLValueString($_SESSION['selectedTables'], "text")
		);

		$mysqli_natal->query($insertSQL);
	}
}

// Geração da declaração SQL
$tmpSQL = "SELECT ";

$tmpFields = explode("~", $_SESSION['selectedFields']);
foreach ($tmpFields as $field) {
	if ($field != "") {
		$tmpSQL .= $field . ", ";
	}
}

$tmpSQL = substr($tmpSQL, 0, -2); // Remove a última vírgula

$tmpSQL .= " FROM ";

$tmpTables = explode("~", $_SESSION['selectedTables']);
foreach ($tmpTables as $table) {
	if ($table != "") {
		$tmpSQL .= $table . ", ";
	}
}

$tmpSQL = substr($tmpSQL, 0, -2); // Remove a última vírgula

if ($_SESSION['appliedConditions'] != "") {
	$tmpSQL .= " WHERE ";
	$tmpCondition = explode("~", $_SESSION['appliedConditions']);
	foreach ($tmpCondition as $condition) {
		if ($condition != "") {
			$tmpSQL .= stripslashes($condition) . " ";
		}
	}
}

if ($_SESSION['lstSortName'] != "") {
	$tmpSQL .= " ORDER BY " . $_SESSION['lstSortName'] . " " . $_SESSION['lstSortOrder'];
}

$_SESSION["tmpSQL"] = $tmpSQL;

$currentPage = $_SERVER["PHP_SELF"];
$maxRows_recSQL = ($_SESSION['txtRecPerPage'] == "") ? "18446744073709551615" : $_SESSION['txtRecPerPage'];
$pageNum_recSQL = (isset($_GET['pageNum_recSQL'])) ? $_GET['pageNum_recSQL'] : 0;
$startRow_recSQL = $pageNum_recSQL * $maxRows_recSQL;

$query_recSQL = $tmpSQL;
$recSQL = $mysqli_natal->query($query_recSQL) or die(dmyError());
$column_count = $recSQL->field_count;

if (isset($_GET['totalRows_recSQL'])) {
	$totalRows_recSQL = $_GET['totalRows_recSQL'];
} else {
	$all_recSQL = $mysqli_natal->query($query_recSQL);
	$totalRows_recSQL = $all_recSQL->num_rows;
}
$totalPages_recSQL = ceil($totalRows_recSQL / $maxRows_recSQL) - 1;

$queryString_recSQL = "";
if (!empty($_SERVER['QUERY_STRING'])) {
	$params = explode("&", $_SERVER['QUERY_STRING']);
	$newParams = array();
	foreach ($params as $param) {
		if (stristr($param, "pageNum_recSQL") == false && stristr($param, "totalRows_recSQL") == false) {
			array_push($newParams, $param);
		}
	}
	if (count($newParams) != 0) {
		$queryString_recSQL = "&" . htmlentities(implode("&", $newParams));
	}
}

$queryString_recSQL = sprintf("&totalRows_recSQL=%d%s", $totalRows_recSQL, $queryString_recSQL);

$design_titulo = "Relatório";
$design_ativo = ""; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Relatórios";
$design_migalha1_link = "index.php";
$design_migalha2_texto = "Exibir relatório";
$design_migalha2_link = "";

include("design1.php");
?>
<link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

<div class="wrapper">
    <!-- Main content -->
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $_SESSION['txtReportName']; ?></h3>
                    </div>
                    <div class="card-body">
                        <?php
                        print("<TABLE class='table table-hover table-bordered dataTable dtr-inline no-footer' id='relatorio' role='grid'><thead> \n");
                        print("<TR ALIGN=LEFT VALIGN=TOP>");
                        for ($column_num = 0; $column_num < $column_count; $column_num++) {
                            $field_name = $recSQL->fetch_field_direct($column_num)->name;
                            print("<TD class='tableHeader'><b>$field_name</b></TD>");
                        }
                        print("</TR></thead><tbody>\n");

                        while ($row = $recSQL->fetch_row()) {
                            print("<TR ALIGN=LEFT VALIGN=TOP>");
                            foreach ($row as $cell) {
                                print("<TD>" . ($cell != "" ? $cell : "&nbsp;") . "</TD>\n");
                            }
                            print("</TR>\n");
                        }
                        ?>
                        </tbody>
                        </table>
                        <br>
                        <button name="cmdBack" type="button" class="btn btn-success m-2" id="cmdBack"
                            onclick="javascript:window.location.href ='setConditions.php'"><i class="fas fa-arrow-left"></i>
                            Editar</button>
                        <button name="cmdExport" type="button" class="btn btn-warning" id="cmdExport"
                            onclick="javascript:window.location.href ='export.php'">
                            <i class="nav-icon fas fa-file-excel"></i> Exportar para Excel
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>


<?php
include('design2.php');
$recSQL->free();
$mysqli_natal->close();
?>
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script>
	$(function () {
		$('#relatorio').DataTable({
			"paging": true,
			"lengthChange": true,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": false,
			"responsive": true,
		});
	});
</script>