<?php 
include("../config.php");
include("../acesso.php");

    if(isset($_POST['valor'])) {
        extract($_POST);
        
        if($valor=="true") {
            if( $consulta = $MySQLi->query("INSERT INTO tb_check_agr (cha_agr_codigo, cha_alt_codigo) values ($adversa,$alternativa)") ){
                echo 1;
            } else echo 0;
        }
        else if($valor=="false") {
            if( $consulta2 = $MySQLi->query("DELETE from tb_check_agr where cha_agr_codigo = $adversa and cha_alt_codigo = $alternativa") ){
                echo 1;
            } else echo 0;
        }
	}else{
        echo 2;
	}
?>