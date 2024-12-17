<?php
include("config.php");
include("acesso.php");

// Verificar se o usuário está logado e tem permissão de administrador ou técnico
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Verificar se o código da mulher e os técnicos foram enviados
if (isset($_POST['codigo'], $_POST['tecnicos'])) {
    $codigo = $_POST['codigo']; // Código da mulher
    $tecnicos = $_POST['tecnicos']; // Técnicos selecionados

    // Adicionar novos técnicos autorizados para visualização
    foreach ($tecnicos as $tecnico) {
        // Inserir cada técnico como autorizado para visualizar o atendimento da mulher
        $MySQLi->query("
            INSERT INTO tb_tecnicos_mulheres (mul_codigo, tec_codigo) 
            VALUES ($codigo, $tecnico)
        ");
    }

    // Redirecionar de volta para a página da mulher com sucesso
    header("Location: mulher-ver.php?codigo=$codigo");
    exit();
} else {
    // Caso os dados não sejam enviados corretamente, redireciona de volta
    header("Location: mulheres.php");
    exit();
}
?>