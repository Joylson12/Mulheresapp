<?php
session_start();
require_once('includes/config.php'); // Incluindo o arquivo de configuração

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados do formulário
    $reportName = isset($_POST['txtReportName']) ? trim($_POST['txtReportName']) : '';
    $selectedFields = isset($_POST['selectedFields']) ? trim($_POST['selectedFields']) : '';
    
    // Opcional: Se 'selectedTables' também for enviado via POST, capture-o
    // Caso contrário, mantenha como está na sessão
    $selectedTables = isset($_SESSION['selectedTables']) ? $_SESSION['selectedTables'] : '';

    // Verifica se as variáveis estão definidas
    if (empty($reportName) || empty($selectedFields) || empty($selectedTables)) {
        echo "Erro: Nome do relatório ou campos selecionados não foram definidos.";
        exit();
    }

    // Prepare a query para inserir os dados
    $query = "INSERT INTO tblreports (txtReportName, selectedFields, selectedTables, status) VALUES (?, ?, ?, ?)";
    $stmt = $MySQLi->prepare($query); // Utilize a variável de conexão correta (ex: $MySQLi)

    $status = 'active'; // Definindo um status padrão

    // Verifica se a preparação da query foi bem-sucedida
    if ($stmt === false) {
        echo "Erro na preparação da query: " . $MySQLi->error;
        exit();
    }

    // Vincula os parâmetros e executa a query
    $stmt->bind_param('ssss', $reportName, $selectedFields, $selectedTables, $status);

    // Execute a query e verifique se foi bem-sucedida
    if ($stmt->execute()) {
        echo "Relatório salvo com sucesso!";
    } else {
        echo "Erro ao salvar o relatório: " . $stmt->error;
    }

    // Limpa as variáveis de sessão, se necessário
    unset($_SESSION['txtReportName']);
    unset($_SESSION['selectedFields']);
    unset($_SESSION['selectedTables']);

    // Redireciona ou exibe a próxima página
    header('Location: selectFields.php'); // Ajuste para a página desejada
    exit();
} else {
    echo "Erro: Método de requisição inválido.";
    exit();
}
?>
