<?php require_once('includes/config.php'); ?>
<?php

function dmyError() {
	print "Table Not Found.";
}

mysql_select_db($database_connDB, $connDB);
$query_recGetFields = "SHOW columns FROM " . $_POST["tableName"];
$recGetFields = mysql_query($query_recGetFields, $connDB) or die(dmyError());
$row_recGetFields = mysql_fetch_array($recGetFields);
$totalRows_recGetFields = mysql_num_rows($recGetFields);
?>
<select name="lstAllFields" size="10" multiple id="lstAllFields"  class="form-control">
	<?php do {  ?>
	<option value="<?php echo ($_POST["tableName"] . ".`" . $row_recGetFields[0]) . "`"?>"><?php echo $row_recGetFields[0]?></option>
	<?php
		} while ($row_recGetFields = mysql_fetch_array($recGetFields));
	  		$rows = mysql_num_rows($recGetFields);
	 		if($rows > 0) {
		  		mysql_data_seek($recGetFields, 0);
		  		$row_recGetFields = mysql_fetch_array($recGetFields);
			}
		?>
</select>
<a href="javascript:cmdSelectFields_onclick();" name="cmdSelectFields" type="button" id="cmdSelectFields" class="btn btn-block bg-gradient-primary"><i class="fas fa-arrow-down"></i> Adicionar Coluna</a>
<?php
mysql_free_result($recGetFields);
?>

