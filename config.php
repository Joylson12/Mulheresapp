<?php ob_start();
	session_start();
	$endereco = "localhost";
	$usuario = "root";
	$senha = "";
	$banco = "mulheresapp_natal";
	$MySQLi = new mysqli ($endereco, $usuario, $senha, $banco, 3306);
	if (mysqli_connect_errno()) {
		die(mysqli_connect_error());
		exit();
	}
	date_default_timezone_set('America/Sao_Paulo');
	mysqli_set_charset($MySQLi, "utf8");
	function data($data){
    	return date("d/m/Y", strtotime($data));
    }
    function hora($data){
    	return date("d/m/Y H:i", strtotime($data));
    }
    function day($data){
    	return date("Y-m-d", strtotime($data));
    }
    function tempo($data){
    	return date("H:i", strtotime($data));
    }
    
    function mes($data){
    	return date("Y-m", strtotime($data));
    }
    function dataEmPortugues ($timestamp) {

        $dia_mes = date("d", $timestamp);// Dia do mês
    
        $mes_num = date("m", $timestamp);// Nome do mês
    
        if($mes_num == 1){
        $mes_nome = "Janeiro";
        }elseif($mes_num == 2){
        $mes_nome = "Fevereiro";
        }elseif($mes_num == 3){
        $mes_nome = "Março";
        }elseif($mes_num == 4){
        $mes_nome = "Abril";
        }elseif($mes_num == 5){
        $mes_nome = "Maio";
        }elseif($mes_num == 6){
        $mes_nome = "Junho";
        }elseif($mes_num == 7){
        $mes_nome = "Julho";
        }elseif($mes_num == 8){
        $mes_nome = "Agosto";
        }elseif($mes_num == 9){
        $mes_nome = "Setembro";
        }elseif($mes_num == 10){
        $mes_nome = "Outubro";
        }elseif($mes_num == 11){
        $mes_nome = "Novembro";
        }else{
        $mes_nome = "Dezembro";
        }
        return $dia_mes." de ".$mes_nome;
    }
    
     function datamulher ($timestamp) { //datas para timeline da mulher

        $dia_mes = date("d", $timestamp);// Dia do mês
    
        $mes_num = date("m", $timestamp);// Nome do mês
    
        $ano = date("Y", $timestamp); //ano
    
        if($mes_num == 1){
        $mes_nome = "Janeiro";
        }elseif($mes_num == 2){
        $mes_nome = "Fevereiro";
        }elseif($mes_num == 3){
        $mes_nome = "Março";
        }elseif($mes_num == 4){
        $mes_nome = "Abril";
        }elseif($mes_num == 5){
        $mes_nome = "Maio";
        }elseif($mes_num == 6){
        $mes_nome = "Junho";
        }elseif($mes_num == 7){
        $mes_nome = "Julho";
        }elseif($mes_num == 8){
        $mes_nome = "Agosto";
        }elseif($mes_num == 9){
        $mes_nome = "Setembro";
        }elseif($mes_num == 10){
        $mes_nome = "Outubro";
        }elseif($mes_num == 11){
        $mes_nome = "Novembro";
        }else{
        $mes_nome = "Dezembro";
        }
        return $mes_nome . ' de ' . $ano;
    }
	
?>