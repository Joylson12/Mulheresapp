<?php
include("config.php");
include("acesso.php");
// Variáveis para definição antes de incluir o design1.php class="nav-link active"

$design_titulo = "Campos de Formulários";
$design_ativo = "m12"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Configurações";
$design_migalha1_link = "";
$design_migalha2_texto = "";
$design_migalha2_link = "";

?>

<?php include("includes/canvas.php");
$id = $_SESSION['id'];
$consulta4 = $MySQLi->query("SELECT tec_senha FROM tb_tecnicos WHERE tec_codigo = $id");
$resultado4 = $consulta4->fetch_assoc();
if (isset($_POST['email'])) {
    $password = $_POST['password'];
    if ($resultado4['tec_senha'] == md5($password)) {
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $consulta = $MySQLi->query("SELECT tec_codigo FROM tb_tecnicos WHERE tec_email = '$email' AND tec_codigo != $id");
        if (!$resultado = $consulta->fetch_assoc()) {
            $consulta2 = $MySQLi->query("UPDATE tb_tecnicos SET tec_email =  '$email', tec_telefone = '$telefone' WHERE tec_codigo = $id");
            $msg = 2;
        } else
            $msg = 1;
        if (isset($_FILES['foto']['name'])) {
            $destino = 'imagens/tecnicos/' . $id . ".jpg";
            $foto = $_FILES['foto']['tmp_name'];
            move_uploaded_file($foto, $destino);
            $imagem = new canvas();
            $imagem->carrega($destino);
            $imagem->redimensiona(300, 300, 'crop');
            $imagem->grava($destino, 88);
        }
        header("Location: configuracoes.php?msg=" . $msg);
    } else
        $msg = 3;
}


if (isset($_POST['senhaatual'])) {
    $senha = $_POST['senhaatual'];
    $novasenha = $_POST['novasenha'];
    $consulta = $MySQLi->query("SELECT tec_senha FROM tb_tecnicos WHERE tec_codigo = $id");
    $resultado = $consulta->fetch_assoc();
    if ($resultado['tec_senha'] != md5($senha))
        $msg2 = 1;
    else {
        $novasenha = md5($novasenha);
        $consulta2 = $MySQLi->query("UPDATE tb_tecnicos SET tec_senha = '$novasenha' WHERE tec_codigo = $id");
        $msg2 = 2;
    }
}

if (isset($_GET['pergunta'])) {
    $pergunta = $_GET['pergunta'];
    $consulta5 = $MySQLi->query("SELECT * FROM tb_perguntas WHERE per_codigo = $pergunta");
    $resultado3 = $consulta5->fetch_assoc();
    $consulta6 = $MySQLi->query("SELECT * FROM tb_alternativas 
                                    JOIN tb_perguntas on per_codigo = alt_per_codigo
                                    WHERE alt_per_codigo = $pergunta
                                    ORDER BY alt_alternativa");
}

if (isset($_GET['msg3']))
    $msg3 = 1;

if (isset($_POST['alternativa'])) {
    $pergunta = $_POST['codPergunta'];
    $alternativa = $_POST['alternativa'];
    $consulta7 = $MySQLi->query("INSERT INTO tb_alternativas (alt_per_codigo, alt_alternativa) VALUES ($pergunta, '$alternativa')");
    header("Location: configuracoes.php?msg3=1&pergunta=$pergunta");
}

$consulta3 = $MySQLi->query("SELECT tec_email, tec_telefone FROM tb_tecnicos WHERE tec_codigo = $id");
$resultado = $consulta3->fetch_assoc();
if (isset($_POST['buscar'])) {
    $buscar = $_POST['buscar'];
    $consulta4 = $MySQLi->query("SELECT * FROM tb_perguntas where per_pergunta like '%$buscar%'");
} else
    $consulta4 = $MySQLi->query("SELECT * FROM tb_perguntas");

if (isset($_GET['alternativa'])) {
    $codigo = $_GET['alternativa'];
    $consulta8 = $MySQLi->query("SELECT alt_ativa FROM tb_alternativas WHERE alt_codigo = $codigo");
    $resultado8 = $consulta8->fetch_assoc();
    if ($resultado8['alt_ativa'] == 1)
        $consulta9 = $MySQLi->query("UPDATE tb_alternativas SET alt_ativa = 0 WHERE alt_codigo = $codigo");
    else
        $consulta9 = $MySQLi->query("UPDATE tb_alternativas SET alt_ativa = 1 WHERE alt_codigo = $codigo");
}

if (isset($_GET['msg']))
    $msg = $_GET['msg'];

include("design1.php");
?>


<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div class="row">

            <div class="col-md-12">

                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Campos de Formulários</h3>
                    </div>

                    <!-- /.card-header -->
                    <!-- form start -->
                    <div class="card-body">
                        <?php if (@$msg2 == 1)
                            echo
                                "<div id='alerta2' class='alert alert-danger' role='alert'>
                        Senha atual incorreta!
                    </div>";
                        ?>
                        <?php if (@$msg2 == 2)
                            echo
                                "<div id='alerta2' class='alert alert-success' role='alert'>
                        Senha atualizada com sucesso!
                    </div>";
                        ?>

                        <p>Neste espaço é possível adicionar alternativas nas perguntas dos questionários.
                            Escolha a pergunta e adicione novas alternativas. <b>Atenção:</b>
                            não é possível apagar ou alterar alternativas que já foram usadas em algum formulário.</p>



                        <?php if (isset($_GET['pergunta'])) { ?>
                            <b>Pergunta: <?php echo $resultado3['per_pergunta'] ?></b>

                            <b></b>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Alternativa</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($resultado4 = $consulta6->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $resultado4['alt_alternativa'] ?></td>
                                            <td>
                                                <!--<button type="button" onclick="window.location.href='?alternativa=<?php echo $resultado4['alt_codigo'] ?>&pergunta=<?php echo $resultado4['alt_per_codigo'] ?>'" 
                      class="btn bg-gradient-primary btn-xs"><?php if ($resultado4['alt_ativa'] == 0)
                          echo 'Habilitar';
                      else
                          echo 'Desabilitar' ?></button>-->
                                                </td>
                                            </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <br>
                            <?php if (@$msg3 == 1)
                                echo
                                    "<div id='alerta2' class='alert alert-success' role='alert'>
                        Alternativa criada com sucesso!
                    </div>";
                            ?>
                            <form action="?" method="post">
                                <div class="input-group input-group-sm">
                                    <input type="hidden" name="codPergunta" value="<?php echo $pergunta ?>">
                                    <input type="text" name="alternativa" class="form-control"
                                        placeholder="Nova alternativa">
                                    <span class="input-group-append">
                                        <button type="submit" class="btn btn-primary btn-flat">Adicionar</button>
                                        <button onclick="window.location.href='configuracoes.php'" type="button"
                                            class="btn btn-danger btn-flat">Voltar</button>
                                    </span>

                                </div>
                            </form>
                        <?php } ?>
                        <hr>

                        <!-- Esta tabela vai ser exibida quando a página de configurações for aberta sem o get ?pergunta=1 -->
                        <?php if (@$msg2 == 3)
                            echo
                                "<div id='alerta2' class='alert alert-success' role='alert'>
                        Pergunta atualizada com sucesso!
                    </div>";
                        ?>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Pergunta</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($resultado2 = $consulta4->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo $resultado2['per_codigo'] ?></td>
                                        <td><?php echo $resultado2['per_pergunta'] ?></td>
                                        <td><a href="?pergunta=<?php echo $resultado2['per_codigo'] ?>"
                                                class="btn btn-sm btn-primary btn-xs"><i class="fas fa-edit"
                                                    title="editar"></i> Editar</a></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                    </div>



                </div>
            </div>

        </div><!-- /.card-body -->








    </div><!-- /.container-fluid -->
</section>



<script>
    function verifica() {
        var senha = document.getElementById('senha').value;
        var confirma = document.getElementById('confirma').value;
        if (senha != confirma) {
            document.getElementById('alerta').style.display = 'block';
            return false;
        } else return true;
    }
</script>


<?php
include("design2.php");
?>

<script src="plugins/jquery/jquery.min.js"></script>
<!-- InputMask -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
<script>
    $(function () {
        $('[data-mask]').inputmask();
    });
</script>