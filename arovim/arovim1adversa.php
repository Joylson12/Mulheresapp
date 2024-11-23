<?php 
include("../config.php");
include("../acesso.php");

    if(isset($_POST['valor'])) {
        extract($_POST);
        if( $consulta = $MySQLi->query("UPDATE tb_agressores set $coluna = '$valor' where agr_codigo = $adversa") ){
                                    echo 1;
                                } else { echo 0; }
 
	}else{
        echo 2;
	}
?>