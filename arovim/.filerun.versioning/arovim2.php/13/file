<?php 
include("../config.php");
include("../acesso.php");

    if(isset($_POST['valor'])) {
        extract($_POST);
        
        if($valor=="true") {
            if( $consulta = $MySQLi->query("INSERT INTO tb_check_mul (chm_mul_codigo, chm_alt_codigo) values ($mulher,$alternativa)") ){
                echo 1;
            } else echo 0;
        }
        else if($valor=="false") {
            if( $consulta2 = $MySQLi->query("DELETE from tb_check_mul where chm_mul_codigo = $mulher and chm_alt_codigo = $alternativa") ){
                echo 1;
            } else echo 0;
        }
	}else{
        echo 2;
	}
?>