<?php 
include("../config.php");
include("../acesso.php");

    if(isset($_POST['valor'])) {
        extract($_POST);
        if( $consulta = $MySQLi->query("UPDATE tb_mulheres set $coluna = '$valor' where mul_codigo = $mulher") ){
                                    echo 1;
                                } else { echo 0; }
 
	}else{
        echo 2;
	}
?>