<?php ob_start();
	$endereco = "localhost";
	$usuario = "mulheres_sistema";
	$senha = "Natal2020";
	$banco = "mulheres_natal";
	$MySQLi = new mysqli ($endereco, $usuario, $senha, $banco, 3306);
	if (mysqli_connect_errno()) {
		die(mysqli_connect_error());
		exit();
	}
	mysqli_set_charset($MySQLi,"utf8");
	date_default_timezone_set('America/Sao_Paulo');
	
?>