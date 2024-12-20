<?php

function geraselect ($codpergunta, $codmulher, $nomecampo, $nomepergunta){
    include("arovim/config.php");
    $consultat1 = $MySQLi->query("SELECT alt_codigo,alt_alternativa FROM tb_alternativas where alt_per_codigo = $codpergunta");
    $consultat2 = $MySQLi->query("SELECT $nomecampo FROM tb_mulheres where mul_codigo = $codmulher");
    $resultadot2 = $consultat2 -> fetch_assoc();
    $imprime= '    <div class="form-group" id="p'.$codpergunta.'">
        <label>'.$nomepergunta.'</label>
        <select name="'.$nomecampo.'" id="'.$nomecampo.'" class="custom-select" onchange="submetevalor(this.value, '.$codmulher.', \''. $nomecampo .'\')">
        <option></option>
        ';
    while ($resultadot1 = $consultat1->fetch_assoc()) {
        $imprime.= '          <option value="'.$resultadot1['alt_codigo'] .'" ';
        if($resultadot1['alt_codigo'] == $resultadot2[$nomecampo]) $imprime.= "selected='selected'";
        $imprime.= '>'.$resultadot1['alt_alternativa'] . '</option>
        ';
    }
    $imprime.= '        </select>
    </div>';
    return $imprime;
}

function geraselectpessoa ($codpergunta, $codpessoa, $nomecampo, $nomepergunta){
    include("arovim/config.php");
    $consultat1 = $MySQLi->query("SELECT alt_codigo,alt_alternativa FROM tb_alternativas where alt_per_codigo = $codpergunta");
    $consultat2 = $MySQLi->query("SELECT $nomecampo FROM tb_pessoas where pes_codigo = $codpessoa");
    $resultadot2 = $consultat2 -> fetch_assoc();
    $imprime= '    <div class="form-group" id="p'.$codpergunta.'">
        <label>'.$nomepergunta.'</label>
        <select name="'.$nomecampo.'" class="custom-select" onchange="submetevalorpessoa(this.value, '.$codpessoa.', \''. $nomecampo .'\')">
        <option></option>
        ';
    while ($resultadot1 = $consultat1->fetch_assoc()) {
        $imprime.= '          <option value="'.$resultadot1['alt_codigo'] .'" ';
        if($resultadot1['alt_codigo'] == $resultadot2[$nomecampo]) $imprime.= "selected='selected'";
        $imprime.= '>'.$resultadot1['alt_alternativa'] . '</option>
        ';
    }
    $imprime.= '        </select>
    </div>';
    return $imprime;
}


function geraselectadversa ($codpergunta, $codadversa, $nomecampo, $nomepergunta){
    include("arovim/config.php");
    $consultat1 = $MySQLi->query("SELECT alt_codigo,alt_alternativa FROM tb_alternativas where alt_per_codigo = $codpergunta");
    $consultat2 = $MySQLi->query("SELECT $nomecampo FROM tb_agressores where agr_codigo = $codadversa");
    $resultadot2 = $consultat2 -> fetch_assoc();
    $imprime= '    <div class="form-group" id="p'.$codpergunta.'">
        <label>'.$nomepergunta.'</label>
        <select id="'.$nomecampo.'" name="'.$nomecampo.'" class="custom-select" onchange="submetevaloradversa(this.value, '.$codadversa.', \''. $nomecampo .'\')">
        <option></option>
        ';
    while ($resultadot1 = $consultat1->fetch_assoc()) {
        $imprime.= '          <option value="'.$resultadot1['alt_codigo'] .'" ';
        if($resultadot1['alt_codigo'] == $resultadot2[$nomecampo]) $imprime.= "selected='selected'";
        $imprime.= '>'.$resultadot1['alt_alternativa'] . '</option>
        ';
    }
    $imprime.= '        </select>
    </div>';
    return $imprime;
}

function geracheckbox ($codpergunta, $codmulher, $nomepergunta, $separador){
    include("arovim/config.php");
    $consultac1 = $MySQLi->query("SELECT alt_codigo,alt_alternativa FROM tb_alternativas where alt_per_codigo = $codpergunta");
    $consultac2 = $MySQLi->query("SELECT alt_codigo FROM tb_check_mul join tb_alternativas on chm_alt_codigo = alt_codigo where alt_per_codigo = $codpergunta and chm_mul_codigo = $codmulher");
    $marcados[] = 0;
    while ($resultadoc2 = $consultac2->fetch_assoc()) {
        $marcados[] = $resultadoc2['alt_codigo'];
    }
    $imprime= '            <div class="checkbox" id="p'.$codpergunta.'">
                <b>'.$nomepergunta.'</b><br>
        ';
    while ($resultadoc1 = $consultac1->fetch_assoc()) {
        $imprime.= '          <label style="font-weight: normal"><input type="checkbox" name="'.$resultadoc1['alt_codigo'].'" value="1" id="a'.$resultadoc1['alt_codigo'].'" 
            onchange="submetecheckbox(this.checked, '.$codmulher.', '.$resultadoc1['alt_codigo'].')" ';
        if(in_array($resultadoc1['alt_codigo'],$marcados)) $imprime.= "checked='true'";
        $imprime.= '> '.$resultadoc1['alt_alternativa'] . '</label>  '.$separador;
    }
    $imprime = rtrim($imprime, $separador);
    
    $imprime.= '        
            </div>';
    return $imprime;
}

function geracheckboxpessoa ($codpergunta, $codpessoa, $nomepergunta, $separador){
    include("arovim/config.php");
    $consultac1 = $MySQLi->query("SELECT alt_codigo,alt_alternativa FROM tb_alternativas where alt_per_codigo = $codpergunta");
    $consultac2 = $MySQLi->query("SELECT alt_codigo FROM tb_check_pes join tb_alternativas on chp_alt_codigo = alt_codigo where alt_per_codigo = $codpergunta and chp_pes_codigo = $codpessoa");
    $marcados[] = 0;
    while ($resultadoc2 = $consultac2->fetch_assoc()) {
        $marcados[] = $resultadoc2['alt_codigo'];
    }
    $imprime= '            <div class="checkbox" id="p'.$codpergunta.'">
                <b>'.$nomepergunta.'</b><br>
        ';
    while ($resultadoc1 = $consultac1->fetch_assoc()) {
        $imprime.= '          <label style="font-weight: normal"><input type="checkbox" name="'.$resultadoc1['alt_codigo'].'" value="1" id="a'.$resultadoc1['alt_codigo'].'" 
            onchange="submetecheckboxpessoa(this.checked, '.$codpessoa.', '.$resultadoc1['alt_codigo'].')" ';
        if(in_array($resultadoc1['alt_codigo'],$marcados)) $imprime.= "checked='true'";
        $imprime.= '> '.$resultadoc1['alt_alternativa'] . '</label>  '.$separador;
    }
    $imprime = rtrim($imprime, $separador);
    
    $imprime.= '        
            </div>';
    return $imprime;
}


function geracheckboxadversa ($codpergunta, $codadversa, $nomepergunta, $separador){
    include("arovim/config.php");
    $consultac1 = $MySQLi->query("SELECT alt_codigo,alt_alternativa FROM tb_alternativas where alt_per_codigo = $codpergunta");
    $consultac2 = $MySQLi->query("SELECT alt_codigo FROM tb_check_agr join tb_alternativas on cha_alt_codigo = alt_codigo where alt_per_codigo = $codpergunta and cha_agr_codigo = $codadversa");
    $marcados[] = 0;
    while ($resultadoc2 = $consultac2->fetch_assoc()) {
        $marcados[] = $resultadoc2['alt_codigo'];
    }
    $imprime= '            <div class="checkbox" id="p'.$codpergunta.'">
                <b>'.$nomepergunta.'</b><br>
        ';
    while ($resultadoc1 = $consultac1->fetch_assoc()) {
        $imprime.= '          <label style="font-weight: normal"><input type="checkbox" name="'.$resultadoc1['alt_codigo'].'" value="1" id="a'.$resultadoc1['alt_codigo'].'" 
            onchange="submetecheckboxadversa(this.checked, '.$codadversa.', '.$resultadoc1['alt_codigo'].')" ';
        if(in_array($resultadoc1['alt_codigo'],$marcados)) $imprime.= "checked='true'";
        $imprime.= '> '.$resultadoc1['alt_alternativa'] . '</label>  '.$separador;
    }
    $imprime = rtrim($imprime, $separador);
    
    $imprime.= '        
            </div>';
    return $imprime;
}

?>