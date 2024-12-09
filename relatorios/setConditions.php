<?php
session_start();
require_once('includes/config.php');

if (isset($_POST["selectedFields"]) && $_POST["selectedFields"] != "") {
    $_SESSION['selectedFields'] = $_POST["selectedFields"]; 
}

$design_titulo = "Relatórios";
$design_ativo = "r3"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "Relatórios";
$design_migalha1_link = "index.php";
$design_migalha2_texto = "Editar: Parte 3";
$design_migalha2_link = "";

include("design1.php");
?>

<script language="javascript" type="text/javascript" src="ajaxlib.js"></script>
<script language="javascript" type="text/javascript">
var lstType;
var lstFieldNames;
var lstConditions;
var lstValueType;
var inputValue;
var lstAppliedConditions;
var appliedConditions;
var txtReportName;
var lstSortName;
var lstSortOrder;
var inputType;

function initVars() {
    lstType = document.getElementById("lstType");
    lstFieldNames = document.getElementById("lstFieldNames");
    lstConditions = document.getElementById("lstConditions");
    lstValueType = document.getElementById("lstValueType");
    inputValue = document.getElementById("inputValue");
    lstAppliedConditions = document.getElementById("lstAppliedConditions");
    appliedConditions = document.getElementById("appliedConditions");
    txtReportName = document.getElementById("txtReportName");
    lstSortName = document.getElementById("lstSortName");
    lstSortOrder = document.getElementById("lstSortOrder");
    inputType = document.getElementById("inputType");
}

function jumpURL(tmpURL) {    
    window.location.href = tmpURL;
}

function displayInput(inputData){
    inputType.innerHTML = inputData;
}

function cmdAdd_onClick(){
    initVars();
    if(validateFields() == 0){
        var tmpCondition;
        if (lstAppliedConditions.options.length == 0) {
            if (lstConditions.value != "LIKE") {
                if (lstValueType.value == "field") {
                    tmpCondition = lstFieldNames.value + " " + lstConditions.value + " " + stripQuotes(inputValue.value);
                } else {
                    tmpCondition = lstFieldNames.value + " " + lstConditions.value + " '" + stripQuotes(inputValue.value) + "'";
                }
            } else {
                if (lstValueType.value == "field") {
                    tmpCondition = lstFieldNames.value + " " + lstConditions.value + " " + stripQuotes(inputValue.value);
                } else {
                    tmpCondition = lstFieldNames.value + " " + lstConditions.value + " '%" + stripQuotes(inputValue.value) + "%'";
                }
            }
        } else {
            if (lstConditions.value != "LIKE") {
                if (lstValueType.value == "field") {
                    tmpCondition = lstType.value + " " + lstFieldNames.value + " " + lstConditions.value + " " + stripQuotes(inputValue.value);
                } else {
                    tmpCondition = lstType.value + " " + lstFieldNames.value + " " + lstConditions.value + " '" + stripQuotes(inputValue.value) + "'";
                }
            } else {
                if (lstValueType.value == "field") {
                    tmpCondition = lstType.value + " " + lstFieldNames.value + " " + lstConditions.value + " " + stripQuotes(inputValue.value);
                } else {
                    tmpCondition = lstType.value + " " + lstFieldNames.value + " " + lstConditions.value + " '%" + stripQuotes(inputValue.value) + "%'";
                }
            }
        }
        var newOption = document.createElement('option');
        var newText = document.createTextNode(tmpCondition);
        newOption.appendChild(newText);
        newOption.setAttribute("value", tmpCondition);
        lstAppliedConditions.appendChild(newOption);
        updateConditions();
    }
}

function updateConditions() {
    appliedConditions.value = "";

    if (lstAppliedConditions.options.length != 0) {
        for (var x = 0; x < lstAppliedConditions.options.length; x++) {
            appliedConditions.value += lstAppliedConditions.options[x].value + " ~";
        }
    }
}

function validateFields() {
    var errorValue = 0;

    if (lstType.value == "") {
        errorValue = 1;
    }

    if (lstFieldNames.value == "") {
        errorValue = 1;
    }

    if (lstConditions.value == "") {
        errorValue = 1;
    }

    if (inputValue.value == "") {
        // errorValue = 1; // Uncomment if you want to enforce input value
    }

    return errorValue;
}

function cmdRemove_onClick() {
    var selIndex = lstAppliedConditions.selectedIndex;
    if (selIndex < 0) return;

    lstAppliedConditions.removeChild(lstAppliedConditions.options.item(selIndex));
    updateConditions();
}

function submitForm() {
    var tmpMsg = "";

    if (txtReportName.value == "") {
        tmpMsg += "O nome do relatório é obrigatório\n";
    }

    if (tmpMsg == "") {
        updateConditions();
        document.frmConditions.submit();
    } else {
        alert(tmpMsg);
    }
}

function stripQuotes(string) {
    return string.replace(/'/g, "");
}

function cmdNew_onClick() {
    if (confirm("Confirme a ação")) {
        window.open("newReport.php", "_self");
    } 
}
</script>

<!-- Main content -->
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Filtros e Classificações do Relatório</h3>
                    </div> 
                    <div class="card-body">
                        <?php if($_SESSION['txtReportName'] != "") {
                            echo "<h2>Edição: " . $_SESSION['txtReportName'] . "</h2>";
                        } else {
                            echo "<h2>Novo Relatório</h2>";
                        } ?>
                        <div id="progress"></div>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Filtros</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label for="lstType" class="col-sm-5 col-form-label">Tipo</label>
                                            <div class="col-sm-7">
                                                <select name="lstType" id="lstType" class="form-control">
                                                    <option value="AND">E</option>
                                                    <option value="OR">OU</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="lstFieldNames" class="col-sm-5 col-form-label">Campo</label>
                                            <div class="col-sm-7">
                                                <select name="lstFieldNames" id="lstFieldNames" class="form-control">
                                                    <?php
                                                    $tmpFields = explode("~", $_SESSION['selectedFields']);
                                                    foreach ($tmpFields as $field) {
                                                        if ($field != "") {
                                                            echo "<option value=\"$field\">$field</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>  
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="lstConditions" class="col-sm-5 col-form-label">Condição</label>
                                            <div class="col-sm-7">
                                                <select name="lstConditions" id="lstConditions" class="form-control">
                                                    <option value="=">Igual</option>
                                                    <option value="<>">Diferente</option>
                                                    <option value=">">Maior que</option>
                                                    <option value="<">Menor que</option>
                                                    <option value=">=">Maior ou igual a</option>
                                                    <option value="<=">Menor ou igual a</option>
                                                    <option value="LIKE">Parecido</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="lstValueType" class="col-sm-5 col-form-label">Tipo de valor</label>
                                            <div class="col-sm-7">
                                                <select name="lstValueType" id="lstValueType" onchange="doAjax('getInputType.php','inputValue=' + this.value,'displayInput','post',0,'progress');" class="form-control">
                                                    <option value=""></option>
                                                    <option value="input">Digitar valor</option>
                                                    <option value="field">Coluna</option>
                                                </select> 
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="lstValueType" class="col-sm-5 col-form-label">Valor</label>
                                            <div class="col-sm-7">
                                                <div id="inputType">-------</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <select name="lstAppliedConditions" size="5" multiple="multiple" id="lstAppliedConditions" class="form-control">
                                            <?php
                                            $tmpCondition = explode("~", $_SESSION['appliedConditions']);
                                            foreach ($tmpCondition as $condition) {
                                                if ($condition != "") {
                                                    echo "<option value=\"$condition\">$condition</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                        <br>
                                        <button class="btn btn-primary" onclick="cmdAdd_onClick();">Adicionar Filtro</button>
                                        <button class="btn btn-danger" onclick="cmdRemove_onClick();">Remover Filtro</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="form-group">
                            <label for="txtReportName" class="col-sm-2 col-form-label">Nome do Relatório</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="txtReportName" id="txtReportName" value="<?php echo $_SESSION['txtReportName']; ?>" />
                            </div>
                        </div>
                        <input type="hidden" name="appliedConditions" id="appliedConditions" />
                        <br>
                        <button class="btn btn-primary" onclick="submitForm();">Salvar</button>
                        <button class="btn btn-secondary" onclick="cmdNew_onClick();">Novo</button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<?php
include("design2.php");
?>
