<?php
include("config.php");
include("acesso.php");
// Variáveis para definição antes de incluir o design1.php class="nav-link active"

$design_titulo = "Você não tem permissão para acessar essa página.
";
$design_ativo = "m2"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Mulheres";
$design_migalha1_link = "";
$design_migalha2_texto = "";
$design_migalha2_link = "";

?>

<?php include("design1.php");
if (isset($_GET['buscar'])) {
    $buscar = $_GET['buscar'];
    $consulta = $MySQLi->query("SELECT mul_codigo,mul_foto,mul_nome,mul_data_nasc,mul_cpf,mul_rg FROM tb_mulheres where soundex(mul_nome) like concat('%',soundex('$buscar'),'%') UNION 
		SELECT mul_codigo,mul_foto,mul_nome,mul_data_nasc,mul_cpf,mul_rg FROM tb_mulheres where mul_nome like '%$buscar%' limit 10");
} else
    $consulta = $MySQLi->query("SELECT mul_codigo,mul_foto,mul_nome,mul_data_nasc,mul_cpf,mul_rg FROM tb_mulheres limit 10");
?>
<!-- DataTables -->
<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

    </div>
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js"></script>

    <?php
    include("design2.php");
    ?>