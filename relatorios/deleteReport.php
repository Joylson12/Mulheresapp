<?php
session_start();
require_once('includes/config.php');

// Verifica se o ID do relatório está na sessão
if (isset($_SESSION['report_id']) && !empty($_SESSION['report_id'])) {
    $reportId = $_SESSION['report_id'];
    
    // Deleta o relatório da tabela tblreports com base no ID
    $deleteSQL = "DELETE FROM tblreports WHERE id = ?";
    $stmt = $mysqli_relatorios->prepare($deleteSQL);
    $stmt->bind_param("i", $reportId);

    if ($stmt->execute()) {
        // Redireciona para a página de confirmação
        $_SESSION["dmyError"] = "Relatório apagado com sucesso!";
        $_SESSION["dmyErrorUrl"] = "index.php";  // Ou qualquer outra página que você preferir
        print "<script language=\"JavaScript\">";
        print "window.location = 'index.php' ";
        print "</script>";
    } else {
        $_SESSION["dmyError"] = "Falha ao apagar o relatório. Tente novamente.";
        $_SESSION["dmyErrorUrl"] = "index.php"; // Ou qualquer outra página de erro
        print "<script language=\"JavaScript\">";
        print "window.location = 'index.php' ";
        print "</script>";
    }

    $stmt->close();
} else {
    $_SESSION["dmyError"] = "Nenhum relatório encontrado para excluir.";
    $_SESSION["dmyErrorUrl"] = "index.php"; // Ou outra página para redirecionar em caso de erro
    print "<script language=\"JavaScript\">";
    print "window.location = 'index.php' ";
    print "</script>";
}

$mysqli_relatorios->close();
?>