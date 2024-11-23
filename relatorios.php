<?php include('config.php');
    $codigo = $_SESSION['id'];
    header("Location: relatorios/loga.php?codigousuarioconectado=$codigo");
?>