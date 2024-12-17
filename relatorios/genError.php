<?php
session_start();

// Verificar se a mensagem de erro está na sessão
if (isset($_SESSION["dmyError"])) {
    $errorMessage = $_SESSION["dmyError"];
    $redirectUrl = $_SESSION["dmyErrorUrl"];
} else {
    $errorMessage = "Ocorreu um erro desconhecido.";
    $redirectUrl = "index.php";  // Página de fallback
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro - MulheresApps</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css"> <!-- Adicione seu CSS -->
</head>
<body>
    <div class="container">
        <h1 class="text-center text-danger">Erro</h1>
        <p class="text-center"><?php echo $errorMessage; ?></p>
        <div class="text-center">
            <a href="<?php echo $redirectUrl; ?>" class="btn btn-primary">Voltar</a>
        </div>
    </div>
</body>
</html>

<?php
// Limpar as variáveis de erro da sessão após exibição
unset($_SESSION["dmyError"]);
unset($_SESSION["dmyErrorUrl"]);
?>