<?php
// Início do arquivo: verifique a inicialização da sessão.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definindo as tabelas visíveis
$dbVisTables = "tb_abrigamentos,tb_acompanhamento_abrigamentos,tb_agressores,tb_alternativas,tb_atendimentos,tb_cargos,tb_chamados,tb_check_agr,tb_check_mul,tb_check_pes,tb_componentes_abrigamento,tb_encaminhamentos,tb_logs,tb_lotacoes,tb_mensagens,tb_mulheres,tb_ocorrencias,tb_perguntas,tb_pessoas,tb_tecnicos";

// Verifica se o usuário está autenticado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
} else {
    $user_id = $_SESSION['id'];  // Obtém o ID do usuário da sessão
}

// Configurações de Conexão ao Banco de Dados
$endereco = "localhost";  // Endereço do servidor MySQL
$usuario = "root";  // Usuário MySQL
$senha = "";  // Senha do MySQL (vazia para local)

// Conexão com o banco de dados mulheresapp_natal
$mysqli_natal = new mysqli($endereco, $usuario, $senha, "mulheresapp_natal");
if ($mysqli_natal->connect_error) {
    die("Falha na conexão com mulheresapp_natal: " . $mysqli_natal->connect_error);
}

// Conexão com o banco de dados mulheresapp_relatorios
$mysqli_relatorios = new mysqli($endereco, $usuario, $senha, "mulheresapp_relatorios");
if ($mysqli_relatorios->connect_error) {
    die("Falha na conexão com mulheresapp_relatorios: " . $mysqli_relatorios->connect_error);
}

// Definindo o charset para a conexão correta
$mysqli_natal->set_charset("utf8");
$mysqli_relatorios->set_charset("utf8"); // Adicionei a configuração do charset para mulheresapp_relatorios

// Definindo a variável $MySQLi como a conexão de relatórios
$MySQLi = $mysqli_relatorios; // Torna o objeto de conexão disponível

// Executa a query para contar as mensagens não lidas no banco mulheresapp_natal
$query = "SELECT COUNT(men_codigo) AS totalMensagens FROM tb_mensagens WHERE men_tec_destinatario = $user_id AND men_lida = 0";
// Substituímos "id_mensagem" por "men_codigo"
$result = $mysqli_natal->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    $totalMensagens = $row['totalMensagens'];
} else {
    echo "Erro na query: " . $mysqli_natal->error;
}

// Definindo o fuso horário
date_default_timezone_set('America/Sao_Paulo');

// Funções de formatação de data e hora
function data($data) {
    return date("d/m/Y", strtotime($data));
}

function hora($data) {
    return date("d/m/Y H:i", strtotime($data));
}

function day($data) {
    return date("Y-m-d", strtotime($data));
}

function tempo($data) {
    return date("H:i", strtotime($data));
}

function mes($data) {
    return date("Y-m", strtotime($data));
}

// Função para formatar a data em português
function dataEmPortugues($timestamp) {
    $dia_mes = date("d", $timestamp);  // Dia do mês
    $mes_num = date("m", $timestamp);  // Número do mês

    // Associando números dos meses aos nomes em português
    $meses = [
        1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril",
        5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto",
        9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro"
    ];

    $mes_nome = $meses[(int)$mes_num];  // Convertendo o número do mês para o nome
    return $dia_mes . " de " . $mes_nome;
}

// Função para datas na timeline das mulheres
function datamulher($timestamp) {
    $dia_mes = date("d", $timestamp);  // Dia do mês
    $mes_num = date("m", $timestamp);  // Número do mês
    $ano = date("Y", $timestamp);  // Ano

    // Associando números dos meses aos nomes em português
    $meses = [
        1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril",
        5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto",
        9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro"
    ];

    $mes_nome = $meses[(int)$mes_num];  // Convertendo o número do mês para o nome
    return $mes_nome . ' de ' . $ano;
}
?>
