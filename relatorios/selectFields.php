<?php
session_start();

// Inicializa as variáveis de sessão se ainda não estiverem definidas
if (!isset($_SESSION['selectedTables'])) {
    $_SESSION['selectedTables'] = ''; // Valor padrão
}
if (!isset($_SESSION['selectedFields'])) {
    $_SESSION['selectedFields'] = ''; // Valor padrão
}
if (!isset($_SESSION['txtReportName'])) {
    $_SESSION['txtReportName'] = ''; // Valor padrão
}

// Verifica os dados POST e atualiza a variável de sessão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["txtReportName"]) && !empty($_POST["txtReportName"])) {
        $_SESSION['txtReportName'] = $_POST["txtReportName"];
    }
    if (isset($_POST["selectedFields"]) && !empty($_POST["selectedFields"])) {
        $_SESSION['selectedFields'] = $_POST["selectedFields"];
    }
}

// Limpa as variáveis de sessão para reiniciar o relatório
if (isset($_POST['reiniciar'])) {
    $_SESSION['selectedFields'] = '';
    $_SESSION['txtReportName'] = '';
}

$design_titulo = "Relatórios";
$design_ativo = "r3"; // Item de menu ativo
$design_migalha1_texto = "Relatórios";
$design_migalha1_link = "index.php";
$design_migalha2_texto = "Selecionar Campos";
$design_migalha2_link = "";

require_once('includes/config.php');
include("design1.php");
?>

<!-- Inclusão dos Scripts -->
<script language="javascript" type="text/javascript" src="ajaxlib.js"></script>
<script language="javascript" type="text/javascript">
    var lstSelectedFields, lstAllFields, cmdNext, selectedFields, dispFields, lstTables;

    function initVars() {
        lstSelectedFields = document.getElementById("lstSelectedFields");
        selectedFields = document.getElementById("selectedFields");
        cmdNext = document.getElementById("cmdNext");
        dispFields = document.getElementById("dispFields");
        lstTables = document.getElementById("lstTables");

        if (lstTables && lstTables.options.length > 0) {
            doAjax('getFieldNames.php', 'tableName=' + encodeURIComponent(lstTables.options[0].value), 'displayFields', 'post', 0, 'progress');
        }

        updateFields(); // Atualiza o estado inicial do botão "Próximo"
    }

    function cmdSelectFields_onclick() {
        if (!lstAllFields) {
            console.error("Elemento lstAllFields não está disponível.");
            alert("Erro: Não foi possível encontrar as colunas disponíveis.");
            return;
        }

        var addIndex = lstAllFields.selectedIndex;
        if (addIndex < 0) {
            alert("Por favor, selecione pelo menos uma coluna para adicionar.");
            return;
        }

        for (let i = 0; i < lstAllFields.options.length; i++) {
            if (lstAllFields.options[i].selected) {
                var tmpFound = false;
                for (let x = 0; x < lstSelectedFields.options.length; x++) {
                    if (lstSelectedFields.options[x].value === lstAllFields.options[i].value) {
                        tmpFound = true;
                        break;
                    }
                }
                if (!tmpFound) {
                    var newOption = document.createElement('option');
                    newOption.text = lstAllFields.options[i].text;
                    newOption.value = lstAllFields.options[i].value;
                    lstSelectedFields.appendChild(newOption);
                    console.log(`Campo adicionado: ${newOption.value}`);
                } else {
                    console.log(`Campo já existe: ${lstAllFields.options[i].value}`);
                }
            }
        }

        updateFields(); // Atualiza os campos
    }

    function cmdRemoveFields_onclick() {
        var selIndex = lstSelectedFields.selectedIndex;
        var itemCount = lstSelectedFields.options.length;
        if (selIndex < 0) {
            alert("Por favor, selecione pelo menos uma coluna para remover.");
            return;
        }

        for (let x = itemCount - 1; x >= 0; x--) {
            if (lstSelectedFields.options[x].selected) {
                console.log(`Campo removido: ${lstSelectedFields.options[x].value}`);
                lstSelectedFields.removeChild(lstSelectedFields.options[x]);
            }
        }

        updateFields();
    }

    function updateFields() {
        selectedFields.value = ""; // Reinicializa o valor
        for (let x = 0; x < lstSelectedFields.options.length; x++) {
            selectedFields.value += lstSelectedFields.options[x].value + "~";
        }

        console.log("Selected fields:", selectedFields.value); // Verifique se os valores estão sendo atualizados

        cmdNext.disabled = selectedFields.value === ""; // Habilita o botão se houver campos selecionados
        console.log("cmdNext disabled status:", cmdNext.disabled); // Verifica se o botão está sendo habilitado corretamente
    }

    function displayFields(fieldData) {
        dispFields.innerHTML = fieldData; // Mostra os campos disponíveis

        // Atualiza a referência para lstAllFields após a inserção do HTML
        lstAllFields = document.getElementById("lstAllFields");
        if (!lstAllFields) {
            console.error("Elemento lstAllFields não encontrado após a chamada AJAX.");
        } else {
            console.log("Elemento lstAllFields encontrado após a chamada AJAX.");
        }

        updateFields(); // Atualiza o estado do botão "Próximo"
    }

    function moveUpList() {
        if (lstSelectedFields.options.length === 0) {
            alert("Não existem itens para mover!");
            return;
        }

        var selected = lstSelectedFields.selectedIndex;
        if (selected === -1) {
            alert("Você deve selecionar um item para mover!");
            return;
        }

        if (selected === 0) {
            alert("O primeiro item da lista não pode ser movido para cima.");
            return;
        }

        // Troca os itens
        var moveText = lstSelectedFields.options[selected - 1].text;
        var moveValue = lstSelectedFields.options[selected - 1].value;
        lstSelectedFields.options[selected - 1].text = lstSelectedFields.options[selected].text;
        lstSelectedFields.options[selected - 1].value = lstSelectedFields.options[selected].value;
        lstSelectedFields.options[selected].text = moveText;
        lstSelectedFields.options[selected].value = moveValue;

        lstSelectedFields.selectedIndex = selected - 1; // Atualiza a seleção
        updateFields(); // Atualiza os campos
    }

    function moveDownList() {
        if (lstSelectedFields.options.length === 0) {
            alert("Não existem itens para mover!");
            return;
        }

        var selected = lstSelectedFields.selectedIndex;
        if (selected === -1) {
            alert("Você deve selecionar um item para mover!");
            return;
        }

        if (selected >= lstSelectedFields.options.length - 1) {
            alert("O último item da lista não pode ser movido para baixo.");
            return;
        }

        // Troca os itens
        var moveText = lstSelectedFields.options[selected + 1].text;
        var moveValue = lstSelectedFields.options[selected + 1].value;
        lstSelectedFields.options[selected + 1].text = lstSelectedFields.options[selected].text;
        lstSelectedFields.options[selected + 1].value = lstSelectedFields.options[selected].value;
        lstSelectedFields.options[selected].text = moveText;
        lstSelectedFields.options[selected].value = moveValue;

        lstSelectedFields.selectedIndex = selected + 1; // Atualiza a seleção
        updateFields(); // Atualiza os campos
    }

    window.onload = initVars;
</script>

<!-- Estrutura do Formulário -->
<form method="post" action="newReport.php">
    <div class="wrapper">
        <!-- Conteúdo principal -->
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Seleção de Colunas do formulário</h3>
                        </div> <!-- /.card-header -->

                        <div class="card-body">
                            <h2>Nome do Relatório:</h2>
                            <input type="text" name="txtReportName" id="txtReportName" class="form-control"
                                placeholder="Digite o nome do relatório"
                                value="<?php echo htmlspecialchars($_SESSION['txtReportName'], ENT_QUOTES); ?>" />

                            <!-- Campo oculto para selectedFields -->
                            <input type="hidden" id="selectedFields" name="selectedFields"
                                value="<?php echo htmlspecialchars($_SESSION['selectedFields'], ENT_QUOTES); ?>" />

                            <div class="row">
                                <div class="col-md-2">
                                    <b>Tabelas:</b>
                                    <select name="lstTables" id="lstTables" class='form-control'
                                        onChange="doAjax('getFieldNames.php','tableName=' + encodeURIComponent(this.value),'displayFields','post',0,'progress');">
                                        <?php
                                        $tmpTables = explode("~", $_SESSION['selectedTables']);
                                        foreach ($tmpTables as $table) {
                                            if ($table != "") {
                                                echo "<option value='$table'>$table</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-5">
                                    <b>Campos Disponíveis:</b>
                                    <div id="dispFields">
                                        <select id="lstAllFields" multiple class="form-control" style="height: 250px;">
                                            <!-- Campos serão preenchidos pelo AJAX -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <b>Campos Selecionados:</b>
                                    <select id="lstSelectedFields" size="10" multiple class="form-control">
                                        <?php
                                        $selectedFieldsArray = explode("~", $_SESSION['selectedFields']);
                                        foreach ($selectedFieldsArray as $field) {
                                            if ($field != "") {
                                                echo "<option value='$field'>$field</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <button type="button" onclick="cmdRemoveFields_onclick();"
                                        class="btn btn-block bg-gradient-primary">Remover Campos</button>
                                </div>

                            </div> <!-- /.row -->

                            <br />
                            <div class="row">
                                <div class="col-md-4">
                                </div>
                            </div>
                            <br />
                            <div class="row">

                            </div>
                            <br />
                            <div class="row">
                                <div class="col-md-4">
                                    <button type="button" onclick="moveUpList();" class="btn btn-warning">Mover para
                                        Cima</button>
                                    <button type="button" onclick="moveDownList();" class="btn btn-warning">Mover para
                                        Baixo</button>
                                </div>
                            </div>
                        </div> <!-- /.card-body -->
                        <div class="card-footer row">
                            <div class="col-11"></div>
                            <div class="col-1">
                                <input type="submit" id="cmdNext" name="cmdNext" value="Próximo" class="btn btn-primary"
                                    disabled />
                            </div>
                        </div>
                    </div> <!-- /.card -->
                </div> <!-- /.container-fluid -->
            </section> <!-- /.content -->
        </div> <!-- /.content-wrapper -->
    </div> <!-- /.wrapper -->
</form>

<?php include("design2.php"); ?>