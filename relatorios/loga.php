<?php include('../config.php');
    if(isset($_GET['codigousuarioconectado']) && $_GET['codigousuarioconectado'] != ''){
        $codigo = $_GET['codigousuarioconectado'];
        $consulta = $MySQLi->query("SELECT tec_codigo, tec_email, tec_apelido, tec_admin, tec_ativo FROM tb_tecnicos WHERE tec_codigo = $codigo");
        if($resultado = $consulta->fetch_assoc()){
            if($resultado['tec_ativo']==0){
	           header("Location: login.php?msg2=5"); 
	        }else{
                $_SESSION['id'] = $resultado['tec_codigo'];
                $_SESSION['email'] = $resultado['tec_email'];
    			$_SESSION['nome'] = $resultado['tec_apelido'];
    			$_SESSION['admin'] = $resultado['tec_admin'];
    			$_SESSION['foto'] =  'imagens/tecnicos/' . $_SESSION['id'] . '.jpg';
    			header("Location: index.php");
	        }
        }else{
            header("Location: /index.php");
        } 
    }else{
        header("Location: /index.php");
    }
 
?>