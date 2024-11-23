<?php
include("config.php");
include("acesso.php");
// Variáveis para definição antes de incluir o design1.php class="nav-link active"

$design_titulo = "Dashboard";
$design_ativo = "m1"; // coloca o class="nav-link active" no menu correto
$design_migalha1_texto = "";
$design_migalha1_link = "";
$design_migalha2_texto = "";
$design_migalha2_link = "";

?>

<?php include("design1.php");
    //if(isset($_GET['msg'])) $msg = 1;
?>


    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        
        <div class="row">
          <div class="col-lg-8">
            <div class="card">
              <div class="card-header">
                <div class="d-flex justify-content-between">
                  <h3 class="card-title">Atendimentos nas últimas semanas</h3>
                  <a href="relatorios.php">Ver Relatório</a>
                </div>
              </div>
              <div class="card-body">
                <div class="d-flex">
                  <p class="d-flex flex-column">
                    <span class="text-bold text-lg">
                    <?php $consulta5 = $MySQLi->query("SELECT count(*) as total from tb_atendimentos where month(ate_data) = month(now()) and year(ate_data) = year(now())"); $resultado5 = $consulta5->fetch_assoc(); echo $resultado5['total'] ?>
                    </span>
                    <span>Atendimentos neste mês</span>
                  </p>
                  <p class="ml-auto d-flex flex-column text-right">
                    <span class="text-success" id="corseta">
                      <i class="fas fa-arrow-up" id="seta"></i> <span id='aumento'>0</span>
                    </span>
                    <span class="text-muted">Desde a semana passada</span>
                  </p>
                </div>
                <!-- /.d-flex -->

                <div class="position-relative mb-4">
                  <canvas id="atendimentos-chart" height="200"></canvas>
                </div>

                <div class="d-flex flex-row justify-content-end">
                  <span class="mr-2">
                    <i class="fas fa-square text-primary"></i> Psicosocial
                  </span>

                  <span>
                    <i class="fas fa-square text-gray"></i> Social &nbsp;
                  </span>
                  
                  <span>
                    <i class="fas fa-square text-success"></i> Psicológico &nbsp;
                  </span>
                  
                  <span>
                    <i class="fas fa-square text-danger"></i> Jurídico
                  </span>
                </div>
              </div>
            </div>
            
            
            
            
            
            <div class="row">
          <div class="col-lg-6">
              
            <div class="card">
              <div class="card-header ">
                <div class="d-flex justify-content-between">
                  <h3 class="card-title">Ocupação da CACC</h3>
                  <a href="relatorios.php">Ver Relatório</a>
                </div>
              </div>
              <div class="card-body">
                <div class="d-flex">
                  <p class="d-flex flex-column">
                    <span class="text-bold text-lg" id="mulhereshoje">0</span>
                    <span>Abrigadas hoje</span>
                  </p>
                  <p class="ml-auto d-flex flex-column text-right">
                    <span class="text-success2" id="pessoashoje">
                    </span>
                    <span class="text-muted">Mulheres e dependentes</span>
                  </p>
                </div>
                <!-- /.d-flex -->

                <div class="position-relative mb-4">
                  <canvas id="abrigamentos-chart" height="200"></canvas>
                </div>

                <div class="d-flex flex-row justify-content-end">
                  <span class="mr-2">
                    <i class="fas fa-square text-primary"></i> Mulheres 
                  </span>

                  <span>
                    <i class="fas fa-square text-gray"></i> Mulheres e familiares
                  </span>
                </div>
              </div>
            </div> 
              
              
          </div>
          <div class="col-lg-6">
          
          
            
            <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Últimos cadastros</h3>

                    <div class="card-tools">
                      <!--<span class="badge badge-danger">8 novas mulheres</span>-->
                    </div>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body p-0">
                    <ul class="users-list clearfix">
                        <?php
                            $consulta12 = $MySQLi->query("select mul_foto, mul_nome, mul_codigo,date_format(mul_data_cadastro,'%Y-%m-%d') as data1,date_format(mul_data_cadastro,'%d/%m') as data2 from tb_mulheres order by mul_data_cadastro desc limit 8");
                            while($resultado12 = $consulta12->fetch_assoc()){
                        ?>
                      <li>
                        <a href="mulher-ver.php?codigo=<?php echo $resultado12['mul_codigo'] ?>"><img src="imagens/mulheres/<?php echo $resultado12['mul_foto'] ?>.jpg"></a>
                        <a class="users-list-name" href="mulher-ver.php?codigo=<?php echo $resultado12['mul_codigo'] ?>"><?php echo $resultado12['mul_nome'] ?></a>
                        <span class="users-list-date"><?php if($resultado12['data1'] == date("Y-m-d")) echo "hoje";
                                                            elseif($resultado12['data1'] == date("Y-m-d", strtotime( '-1 days' ))) echo "ontem";
                                                            elseif($resultado12['data1'] == date("Y-m-d", strtotime( '-2 days' ))) echo "anteontem";
                                                            else echo $resultado12['data2']; ?></span>
                      </li>
                      <?php } ?>
                    </ul>
                    <!-- /.users-list -->
                  </div>
                  <!-- /.card-body -->
                  <div class="card-footer text-center">
                    <a href="mulheres.php">Ver todas</a>
                  </div>
                  <!-- /.card-footer -->
                </div>
            
          
          </div>
        </div>
            
            
            
            
            
            
          </div>
          <!-- /.col-md-6 -->
          <div class="col-lg-4">
            
            <!-- /.card -->
            
            
            <div class="card ">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="far fa-chart-bar"></i>
                  Mulheres por bairro
                </h3>
              </div>
              <div class="card-body">
                
    <style type="text/css">

        /* Specific mapael css class are below
         * 'mapael' class is added by plugin
        */

        .mapael .map {
            position: relative;
        }

        .mapael .mapTooltip {
            position: absolute;
            background-color: #fff;
            moz-opacity: 0.70;
            opacity: 0.70;
            filter: alpha(opacity=70);
            border-radius: 10px;
            padding: 10px;
            z-index: 1000;
            max-width: 200px;
            display: none;
            color: #343434;
        }
    </style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.2.7/raphael.min.js" charset="utf-8"></script>
<script src="plugins/mapas/jquery.mapael.js" charset="utf-8"></script>
<script src="plugins/mapas/grande_natal.js" charset="utf-8"></script>

    <script type="text/javascript">
        $(function () {
            $(".mapcontainer").mapael({
                map: {
                    name: "grande_natal",
                    defaultArea: {
                        attrs: {
                            stroke: "#fff",
                            "stroke-width": 1
                        },
                        attrsHover: {
                            "stroke-width": 2
                        }
                    }
                },
                legend: {
                    area: {
                        title: "Casos registrados por Bairro",
                        slices: [
                            {
                                max: -1,
                                attrs: {
                                    fill: "#808080"
                                },
                                label: "Não disponível"
                            },
                            {
                                min: 0,
                                max: 1,
                                attrs: {
                                    fill: "#97e766"
                                },
                                label: "Nenhum"
                            },
                            {
                                min: 1,
                                max: 3,
                                attrs: {
                                    fill: "#7fd34d"
                                },
                                label: "Até 3 casos"
                            },
                            {
                                min: 4,
                                max: 10,
                                attrs: {
                                    fill: "#5faa32"
                                },
                                label: "até 10 casos"
                            },
                            {
                                min: 11,
                                attrs: {
                                    fill: "#3f7d1a"
                                },
                                label: "Mais de 10 casos"
                            }
                        ]
                    }
                },
                areas: {
                    <?php
                        $consulta5 = $MySQLi->query("select a.alt_alternativa as bairro,mul_bairro, mul_cidade, b.alt_alternativa as cidade,count(*) as total from tb_mulheres 
                        join tb_alternativas a on a.alt_codigo = mul_bairro join tb_alternativas b on b.alt_codigo = mul_cidade 
                        group by a.alt_alternativa, mul_bairro, b.alt_alternativa, mul_cidade");
                        while ($resultado5 = $consulta5->fetch_assoc()){
                            if($resultado5['cidade']=="Natal"){
                                echo '
                                "b'. $resultado5["mul_bairro"] .'": {
                                    value: "'.$resultado5["total"].'",
                                    tooltip: {content: "<span style=\"font-weight:bold;\">'.$resultado5['bairro'].'</span><br />Casos : '.$resultado5['total'].'"}
                                },';
                            }
                            else {
                                
                                echo '
                                "'. $resultado5["cidade"] .'": {
                                    value: "'.$resultado5["total"].'",
                                    tooltip: {content: "<span style=\"font-weight:bold;\">'.$resultado5['cidade'].'</span><br />Casos : '.$resultado5['total'].'"}
                                },
                                ';
                                
                            }
                            
                        }
                    
                    ?>
                    
                    "viacosteira": {
                        value: "-1",
                        tooltip: {content: "<span style=\"font-weight:bold;\">Via Costeira</span><br />"}
                    }
                }
            });
        });
    </script>
                <div class="mapcontainer">
                    <div class="map">
                    </div>
                    <div class="areaLegend">
                    </div>
                </div>
                
                
              </div>
              <!-- /.card-body-->
            </div> 

            
          </div>
          <!-- /.col-md-6 -->
        </div>
        
        
        
        
      </div><!-- /.container-fluid -->
    </section>

<?php
include("design2.php");
?>

<script src="plugins/chart.js/Chart.min.js"></script>

<!--<script src="plugins/flot/jquery.flot.js"></script>-->
<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
<!--<script src="plugins/flot-old/jquery.flot.resize.min.js"></script>-->
<!-- FLOT PIE PLUGIN - also used to draw donut charts -->
<!--<script src="plugins/flot-old/jquery.flot.pie.min.js"></script>-->
<script>
    
    // var donutData = [
    //   {
    //     label: 'Series2',
    //     data : 30,
    //     color: '#3c8dbc'
    //   },
    //   {
    //     label: 'Series3',
    //     data : 20,
    //     color: '#0073b7'
    //   },
    //   {
    //     label: 'Series4',
    //     data : 50,
    //     color: '#00c0ef'
    //   }
    // ]
    // $.plot('#donut-chart', donutData, {
    //   series: {
    //     pie: {
    //       show       : true,
    //       radius     : 1,
    //       innerRadius: 0.5,
    //       label      : {
    //         show     : true,
    //         radius   : 2 / 3,
    //         formatter: labelFormatter,
    //         threshold: 0.1
    //       }

    //     }
    //   },
    //   legend: {
    //     show: false
    //   }
    // })
    /*
     * END DONUT CHART
     */
</script>

<script>
    $(function () {
  'use strict'

  var ticksStyle = {
    fontColor: '#495057',
    fontStyle: 'bold'
  }

  var mode      = 'index'
  var intersect = true

<?php
    $consulta5 = $MySQLi->query("select count(abrigamentos) as mulheres, sum(total) as pessoas from ( SELECT count(*) as abrigamentos, 
    count(cab_pes_codigo)+1 as total FROM `tb_abrigamentos` left join tb_componentes_abrigamento on cab_abr_codigo = abr_codigo WHERE 
    (date_format(abr_data_inicio,'%Y-%m-%d') <= current_date) and (date_format(abr_data_fim,'%Y-%m-%d') >= current_date or abr_data_fim is null) group by abr_codigo ) as b");
    $resultado5 = $consulta5->fetch_assoc();
    
    $consulta6 = $MySQLi->query("select count(abrigamentos) as mulheres, sum(total) as pessoas from ( SELECT count(*) as abrigamentos, 
    count(cab_pes_codigo)+1 as total FROM `tb_abrigamentos` left join tb_componentes_abrigamento on cab_abr_codigo = abr_codigo WHERE 
    (date_format(abr_data_inicio,'%Y-%m-%d') <= subdate(current_date, 1)) and (date_format(abr_data_fim,'%Y-%m-%d') >= subdate(current_date, 1) or abr_data_fim is null) group by abr_codigo ) as b");
    $resultado6 = $consulta6->fetch_assoc();
    
    $consulta7 = $MySQLi->query("select count(abrigamentos) as mulheres, sum(total) as pessoas from ( SELECT count(*) as abrigamentos, 
    count(cab_pes_codigo)+1 as total FROM `tb_abrigamentos` left join tb_componentes_abrigamento on cab_abr_codigo = abr_codigo WHERE 
    (date_format(abr_data_inicio,'%Y-%m-%d') <= subdate(current_date, 2)) and (date_format(abr_data_fim,'%Y-%m-%d') >= subdate(current_date, 2) or abr_data_fim is null) group by abr_codigo ) as b");
    $resultado7 = $consulta7->fetch_assoc();
    
    $consulta8 = $MySQLi->query("select count(abrigamentos) as mulheres, sum(total) as pessoas from ( SELECT count(*) as abrigamentos, 
    count(cab_pes_codigo)+1 as total FROM `tb_abrigamentos` left join tb_componentes_abrigamento on cab_abr_codigo = abr_codigo WHERE 
    (date_format(abr_data_inicio,'%Y-%m-%d') <= subdate(current_date, 3)) and (date_format(abr_data_fim,'%Y-%m-%d') >= subdate(current_date, 3) or abr_data_fim is null) group by abr_codigo ) as b");
    $resultado8 = $consulta8->fetch_assoc();
    
    $consulta9 = $MySQLi->query("select count(abrigamentos) as mulheres, sum(total) as pessoas from ( SELECT count(*) as abrigamentos, 
    count(cab_pes_codigo)+1 as total FROM `tb_abrigamentos` left join tb_componentes_abrigamento on cab_abr_codigo = abr_codigo WHERE 
    (date_format(abr_data_inicio,'%Y-%m-%d') <= subdate(current_date, 4)) and (date_format(abr_data_fim,'%Y-%m-%d') >= subdate(current_date, 4) or abr_data_fim is null) group by abr_codigo ) as b");
    $resultado9 = $consulta9->fetch_assoc();
    
    $consulta10 = $MySQLi->query("select count(abrigamentos) as mulheres, sum(total) as pessoas from ( SELECT count(*) as abrigamentos, 
    count(cab_pes_codigo)+1 as total FROM `tb_abrigamentos` left join tb_componentes_abrigamento on cab_abr_codigo = abr_codigo WHERE 
    (date_format(abr_data_inicio,'%Y-%m-%d') <= subdate(current_date, 5)) and (date_format(abr_data_fim,'%Y-%m-%d') >= subdate(current_date, 5) or abr_data_fim is null) group by abr_codigo ) as b");
    $resultado10 = $consulta10->fetch_assoc();
    
    $consulta11 = $MySQLi->query("select count(abrigamentos) as mulheres, sum(total) as pessoas from ( SELECT count(*) as abrigamentos, 
    count(cab_pes_codigo)+1 as total FROM `tb_abrigamentos` left join tb_componentes_abrigamento on cab_abr_codigo = abr_codigo WHERE 
    (date_format(abr_data_inicio,'%Y-%m-%d') <= subdate(current_date, 6)) and (date_format(abr_data_fim,'%Y-%m-%d') >= subdate(current_date, 6) or abr_data_fim is null) group by abr_codigo ) as b");
    $resultado11 = $consulta11->fetch_assoc();
    
    $d1 = date("d/m");
    $d2 = date("d/m", strtotime( '-1 days' ) );
    $d3 = date("d/m", strtotime( '-2 days' ) );
    $d4 = date("d/m", strtotime( '-3 days' ) );
    $d5 = date("d/m", strtotime( '-4 days' ) );
    $d6 = date("d/m", strtotime( '-5 days' ) );
    $d7 = date("d/m", strtotime( '-6 days' ) );
?>
    

  var $abrigamentosChart = $('#abrigamentos-chart')
  var abrigamentosChart  = new Chart($abrigamentosChart, {
    type   : 'bar',
    data   : {
      labels  : [<?php echo "'".$d7 . "','" . $d6 . "','" . $d5 . "','" . $d4 . "','" . $d3 . "','" . $d2 . "','" . $d1 . "'" ?>],
      datasets: [
        {
          backgroundColor: '#007bff',
          borderColor    : '#007bff',
          data           : [<?php echo $resultado11['mulheres'] . ',' . $resultado10['mulheres'] . ',' . $resultado9['mulheres'] . ',' . $resultado8['mulheres'] . 
          ',' . $resultado7['mulheres'] . ',' . $resultado6['mulheres'] . ',' . $resultado5['mulheres'] ?>]
        },
        {
          backgroundColor: '#ced4da',
          borderColor    : '#ced4da',
          data           : [<?php echo $resultado11['pessoas'] . ',' . $resultado10['pessoas'] . ',' . $resultado9['pessoas'] . ',' . $resultado8['pessoas'] . 
          ',' . $resultado7['pessoas'] . ',' . $resultado6['pessoas'] . ',' . $resultado5['pessoas'] ?>]
        }
      ]
    },
    options: {
      maintainAspectRatio: false,
      tooltips           : {
        mode     : mode,
        intersect: intersect
      },
      hover              : {
        mode     : mode,
        intersect: intersect
      },
      legend             : {
        display: false
      },
      scales             : {
        yAxes: [{
          // display: false,
          gridLines: {
            display      : true,
            lineWidth    : '4px',
            color        : 'rgba(0, 0, 0, .2)',
            zeroLineColor: 'transparent'
          },
          ticks    : $.extend({
            beginAtZero: true,

            // Include a dollar sign in the ticks
            callback: function (value, index, values) {
              if (value >= 1000) {
                value /= 1000
                value += 'k'
              }
              return '$' + value
            }
          }, ticksStyle)
        }],
        xAxes: [{
          display  : true,
          gridLines: {
            display: false
          },
          ticks    : ticksStyle
        }]
      }
    }
  })

<?php
    $semana1 = (new \DateTime())->modify('tomorrow')->modify('previous monday')->format('d/m/Y');
    $semana2 = DateTime::createFromFormat('d/m/Y', $semana1)->modify('previous monday')->format('d/m/Y');
    $semana3 = DateTime::createFromFormat('d/m/Y', $semana2)->modify('previous monday')->format('d/m/Y');
    $semana4 = DateTime::createFromFormat('d/m/Y', $semana3)->modify('previous monday')->format('d/m/Y');
    $semana5 = DateTime::createFromFormat('d/m/Y', $semana4)->modify('previous monday')->format('d/m/Y');
    $semana6 = DateTime::createFromFormat('d/m/Y', $semana5)->modify('previous monday')->format('d/m/Y');
    $semana7 = DateTime::createFromFormat('d/m/Y', $semana6)->modify('previous monday')->format('d/m/Y');
    
    $linha1 = [0,0,0,0,0,0,0];
    $consulta = $MySQLi->query("SELECT * from vw_atendimentos_semanais where tipo='Psicossocial'");
    while($resultado = $consulta->fetch_assoc()){
        if($resultado['dia'] == $semana1) $linha1[0] = $resultado['atendimentos'];
        if($resultado['dia'] == $semana2) $linha1[1] = $resultado['atendimentos'];
        if($resultado['dia'] == $semana3) $linha1[2] = $resultado['atendimentos'];
        if($resultado['dia'] == $semana4) $linha1[3] = $resultado['atendimentos'];
        if($resultado['dia'] == $semana5) $linha1[4] = $resultado['atendimentos'];
        if($resultado['dia'] == $semana6) $linha1[5] = $resultado['atendimentos'];
        if($resultado['dia'] == $semana7) $linha1[6] = $resultado['atendimentos'];
    }
    $linha2 = [0,0,0,0,0,0,0];
    $consulta2 = $MySQLi->query("SELECT * from vw_atendimentos_semanais where tipo='Social'");
    while($resultado2 = $consulta2->fetch_assoc()){
        if($resultado2['dia'] == $semana1) $linha2[0] = $resultado2['atendimentos'];
        if($resultado2['dia'] == $semana2) $linha2[1] = $resultado2['atendimentos'];
        if($resultado2['dia'] == $semana3) $linha2[2] = $resultado2['atendimentos'];
        if($resultado2['dia'] == $semana4) $linha2[3] = $resultado2['atendimentos'];
        if($resultado2['dia'] == $semana5) $linha2[4] = $resultado2['atendimentos'];
        if($resultado2['dia'] == $semana6) $linha2[5] = $resultado2['atendimentos'];
        if($resultado2['dia'] == $semana7) $linha2[6] = $resultado2['atendimentos'];
    }
    $linha3 = [0,0,0,0,0,0,0];
    $consulta3 = $MySQLi->query("SELECT * from vw_atendimentos_semanais where tipo='Psicológico'");
    while($resultado3 = $consulta3->fetch_assoc()){
        if($resultado3['dia'] == $semana1) $linha3[0] = $resultado3['atendimentos'];
        if($resultado3['dia'] == $semana2) $linha3[1] = $resultado3['atendimentos'];
        if($resultado3['dia'] == $semana3) $linha3[2] = $resultado3['atendimentos'];
        if($resultado3['dia'] == $semana4) $linha3[3] = $resultado3['atendimentos'];
        if($resultado3['dia'] == $semana5) $linha3[4] = $resultado3['atendimentos'];
        if($resultado3['dia'] == $semana6) $linha3[5] = $resultado3['atendimentos'];
        if($resultado3['dia'] == $semana7) $linha3[6] = $resultado3['atendimentos'];
    }
    $linha4 = [0,0,0,0,0,0,0];
    $consulta4 = $MySQLi->query("SELECT * from vw_atendimentos_semanais where tipo='Outro'");
    while($resultado4 = $consulta4->fetch_assoc()){
        if($resultado4['dia'] == $semana1) $linha4[0] = $resultado4['atendimentos'];
        if($resultado4['dia'] == $semana2) $linha4[1] = $resultado4['atendimentos'];
        if($resultado4['dia'] == $semana3) $linha4[2] = $resultado4['atendimentos'];
        if($resultado4['dia'] == $semana4) $linha4[3] = $resultado4['atendimentos'];
        if($resultado4['dia'] == $semana5) $linha4[4] = $resultado4['atendimentos'];
        if($resultado4['dia'] == $semana6) $linha4[5] = $resultado4['atendimentos'];
        if($resultado4['dia'] == $semana7) $linha4[6] = $resultado4['atendimentos'];
    }
    $totalesta = (($linha1[0]+$linha2[0]+$linha3[0]+$linha4[0])/($linha1[1]+$linha2[1]+$linha3[1]+$linha4[1]+0.001)-1)*100;
    
?>

  var $atendimentosChart = $('#atendimentos-chart')
  var atendimentosChart  = new Chart($atendimentosChart, {
    data   : {
      labels  : ['<?php echo $semana7."','".$semana6."','".$semana5."','".$semana4."','".$semana3."','".$semana2."','".$semana1 ?>'],
      datasets: [{
        type                : 'line',
        data                : [<?php echo $linha1[6].",".$linha1[5].",".$linha1[4].",".$linha1[3].",".$linha1[2].",".$linha1[1].",".$linha1[0] ?>],
        backgroundColor     : 'transparent',
        borderColor         : '#007bff',
        pointBorderColor    : '#007bff',
        pointBackgroundColor: '#007bff',
        fill                : false
        // pointHoverBackgroundColor: '#007bff',
        // pointHoverBorderColor    : '#007bff'
      },
        {
          type                : 'line',
          data                : [<?php echo $linha2[6].",".$linha2[5].",".$linha2[4].",".$linha2[3].",".$linha2[2].",".$linha2[1].",".$linha2[0] ?>],
          backgroundColor     : 'tansparent',
          borderColor         : '#ced4da',
          pointBorderColor    : '#ced4da',
          pointBackgroundColor: '#ced4da',
          fill                : false
          // pointHoverBackgroundColor: '#ced4da',
          // pointHoverBorderColor    : '#ced4da'
        },
        {
          type                : 'line',
          data                : [<?php echo $linha3[6].",".$linha3[5].",".$linha3[4].",".$linha3[3].",".$linha3[2].",".$linha3[1].",".$linha3[0] ?>],
          backgroundColor     : 'tansparent',
          borderColor         : '#28A745',
          pointBorderColor    : '#28A745',
          pointBackgroundColor: '#28A745',
          fill                : false
          // pointHoverBackgroundColor: '#ced4da',
          // pointHoverBorderColor    : '#ced4da'
        },
        {
          type                : 'line',
          data                : [<?php echo $linha4[6].",".$linha4[5].",".$linha4[4].",".$linha4[3].",".$linha4[2].",".$linha4[1].",".$linha4[0] ?>],
          backgroundColor     : 'tansparent',
          borderColor         : '#DC3545',
          pointBorderColor    : '#DC3545',
          pointBackgroundColor: '#DC3545',
          fill                : false
          // pointHoverBackgroundColor: '#ced4da',
          // pointHoverBorderColor    : '#ced4da'
        }]
    },
    options: {
      maintainAspectRatio: false,
      tooltips           : {
        mode     : mode,
        intersect: intersect
      },
      hover              : {
        mode     : mode,
        intersect: intersect
      },
      legend             : {
        display: false
      },
      scales             : {
        yAxes: [{
          // display: false,
          gridLines: {
            display      : true,
            lineWidth    : '4px',
            color        : 'rgba(0, 0, 0, .2)',
            zeroLineColor: 'transparent'
          },
          ticks    : $.extend({
            beginAtZero : true
            //,
            //suggestedMax: 10
          }, ticksStyle)
        }],
        xAxes: [{
          display  : true,
          gridLines: {
            display: false
          },
          ticks    : ticksStyle
        }]
      }
    }
  })
})
    
    document.getElementById('aumento').innerHTML = "<?php echo floor($totalesta) ?>%";
    <?php
        if($totalesta<0) {
            echo "
            document.getElementById('seta').classList.remove('fa-arrow-up');
            document.getElementById('seta').classList.add('fa-arrow-down');
            document.getElementById('corseta').classList.remove('text-success');
            document.getElementById('corseta').classList.add('text-danger');
            ";
        }
    ?>
    document.getElementById('mulhereshoje').innerHTML = <?php echo $resultado5['mulheres']; ?>;
    document.getElementById('pessoashoje').innerHTML = <?php echo $resultado5['pessoas']; ?>;
    
    
</script>
