<?php 
include("../config.php");
include("../acesso.php");

    if(isset($_POST['valor'])) {
        extract($_POST);
        if($coluna == 'ate_tec_codigo2' && $valor == '0'){
            if( $consulta = $MySQLi->query("UPDATE tb_atendimentos set $coluna = null where ate_codigo = $atendimento")){
                 echo 1;
            } else { echo 0; }
            
        }
        else if( $consulta = $MySQLi->query("UPDATE tb_atendimentos set $coluna = '".addslashes($valor)."' where ate_codigo = $atendimento") ){
                                    echo 1;
                                } else { echo 0; }
 
	}else{
        echo 2;
	}
?>