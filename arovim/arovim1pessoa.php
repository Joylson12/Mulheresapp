<?php 
include("../config.php");
include("../acesso.php");

    if(isset($_POST['valor'])) {
        extract($_POST);
        if( $consulta = $MySQLi->query("UPDATE tb_pessoas set $coluna = '$valor' where pes_codigo = $pessoa") ){
                                    echo 1;
                                } else { echo 0; }
 
	}else{
        echo 2;
	}
?>