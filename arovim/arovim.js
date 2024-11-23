function submetevalor(v1, m1, c1){
    $.ajax({
        type: 'POST',
        url: 'arovim/arovim1.php',
        data: {
            valor: v1,
            coluna: c1,
            mulher: m1
        },
        success: function(retorna){
            if(retorna == "1"){
		        toastr.success('Salvo');
    	    }
    	    else if (retorna == "2"){
    		    toastr.error('Falha na conexão');
    	    }
    	    else {
    	        toastr.error('Falha na consulta');
    	    }
        },
        async: false
    });
}

function submetecheckbox(v1, m1, a1){
    $.ajax({
        type: 'POST',
        url: 'arovim/arovim2.php',
        data: {
            valor: v1,
            mulher: m1,
            alternativa: a1
        },
        success: function(retorna){
            // toastr.success(retorna);
    	    if(retorna == "1"){
    		    toastr.success('Salvo');
    	    }
    	    else if (retorna == "2"){
    		    toastr.error('Falha na conexão');
    	    }
    	    else {
    	        toastr.error('Falha na consulta');
    	    }
        },
        async: false
    });
    
}
function submetetruefalse(v1, m1, c1){
    var v2;
    v1 === true ? v2 = 1 : v2 = 0;
    $.ajax({
        type: 'POST',
        url: 'arovim/arovim1.php',
        data: {
            valor: v2,
            coluna: c1,
            mulher: m1
        },
        success: function(retorna){
            if(retorna == "1"){
		        toastr.success('Salvo');
    	    }
    	    else if (retorna == "2"){
    		    toastr.error('Falha na conexão');
    	    }
    	    else {
    	        toastr.error('Falha na consulta');
    	    }
        },
        async: false
    });
}

function submetetruefalsepessoa(v1, m1, c1){
    var v2;
    v1 === true ? v2 = 1 : v2 = 0;
    $.ajax({
        type: 'POST',
        url: 'arovim/arovim1pessoa.php',
        data: {
            valor: v2,
            coluna: c1,
            pessoa: m1
        },
        success: function(retorna){
            if(retorna == "1"){
		        toastr.success('Salvo');
    	    }
    	    else if (retorna == "2"){
    		    toastr.error('Falha na conexão');
    	    }
    	    else {
    	        toastr.error('Falha na consulta');
    	    }
        },
        async: false
    });
}

function submetevalorpessoa(v1, m1, c1){
    $.ajax({
        type: 'POST',
        url: 'arovim/arovim1pessoa.php',
        data: {
            valor: v1,
            coluna: c1,
            pessoa: m1
        },
        success: function(retorna){
            if(retorna == "1"){
		        toastr.success('Salvo');
    	    }
    	    else if (retorna == "2"){
    		    toastr.error('Falha na conexão');
    	    }
    	    else {
    	        toastr.error('Falha na consulta');
    	    }
        },
        async: false
    });
}

function submetevaloradversa(v1, m1, c1){
    $.ajax({
        type: 'POST',
        url: 'arovim/arovim1adversa.php',
        data: {
            valor: v1,
            coluna: c1,
            adversa: m1
        },
        success: function(retorna){
            if(retorna == "1"){
		        toastr.success('Salvo');
    	    }
    	    else if (retorna == "2"){
    		    toastr.error('Falha na conexão');
    	    }
    	    else {
    	        toastr.error('Falha na consulta');
    	    }
        },
        async: false
    });
}

function submetecheckboxadversa(v1, m1, a1){
    $.ajax({
        type: 'POST',
        url: 'arovim/arovim2adversa.php',
        data: {
            valor: v1,
            adversa: m1,
            alternativa: a1
        },
        success: function(retorna){
            // toastr.success(retorna);
    	    if(retorna == "1"){
    		    toastr.success('Salvo');
    	    }
    	    else if (retorna == "2"){
    		    toastr.error('Falha na conexão');
    	    }
    	    else {
    	        toastr.error('Falha na consulta');
    	    }
        },
        async: false
    });
    
}
function submetetruefalseadversa(v1, m1, c1){
    var v2;
    v1 === true ? v2 = 1 : v2 = 0;
    $.ajax({
        type: 'POST',
        url: 'arovim/arovim1adversa.php',
        data: {
            valor: v2,
            coluna: c1,
            adversa: m1
        },
        success: function(retorna){
            if(retorna == "1"){
		        toastr.success('Salvo');
    	    }
    	    else if (retorna == "2"){
    		    toastr.error('Falha na conexão');
    	    }
    	    else {
    	        toastr.error('Falha na consulta');
    	    }
        },
        async: false
    });
}

function submetevaloratendimento(v1, m1, c1){
    $.ajax({
        type: 'POST',
        url: 'arovim/arovim1atendimentos.php',
        data: {
            valor: v1,
            coluna: c1,
            atendimento: m1
        },
        success: function(retorna){
            if(retorna == "1"){
		        toastr.success('Salvo');
    	    }
    	    else if (retorna == "2"){
    		    toastr.error('Falha na conexão');
    	    }
    	    else {
    	        toastr.error('Falha na consulta');
    	    }
        },
        async: false
    });
}