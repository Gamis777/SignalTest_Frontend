@extends('layout.principal')
@section('tituloModulo', 'Reporte de pruebas')
@section('contenido')

<div class="row">
    <div class="container">
        <div class="row">
            <div class='col-sm-6'>
                <div class="form-group">
                    <div class='input-group date' id='datetimepicker1'>
                        <input type='text' class="form-control" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                $(function () {
                    $('#datetimepicker1').datetimepicker();
                });
            </script>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <!-- /.card-header -->
            <!-- <button onclick="changeColor()"  type="button" id="btn-changecolor" class="btn btn-info" data-dismiss="modal">PDF</button> -->
            <div class="card-body">
                <table id="tabla_pruebas" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nombre</th>
                            <th>Matriz</th>
                            <th>Email</th>
                            <th>Fecha de Inicio</th>                      
                            <th>Tipo de Lanzamiento</th>
                            <th>Programación</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th class="text-center">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<div class="modal fade" id="modal-detalle">
    <div class="modal-dialog modal-lg" style="max-width:95%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_modal" ></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body align-items-center d-flex justify-content-center" id="grafico">
                </div>
                <button onclick="generatePDF()"  type="button" id="pdf_generate" class="btn btn-info" data-dismiss="modal">PDF</button>
                <div class="div_detalle" style="overflow-x: auto;">
                    <table id="detalle_prueba" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Origen</th>
                                <th>Destino</th>
                                <th>Resultado Salida</th>
                                <th>Hora Salida</th>
                                <th>HangUp Cause</th>
                                <th>Resultado Entrada</th>
                                <th>Hora Entrada</th>
                                <th>Resultado Final</th>
                                <th>MOS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!--<tr>
                                <td>Canal 1</td>
                                <td>OK</td>
                            </tr>-->
                        </tbody>
                    </table>
                </div>    
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                <!-- <button type="button" class="btn btn-primary">Guardar</button> -->
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
@endsection
@section('javascript')
<script src="js/jsPDF/dist/jspdf.min.js"></script>
<script>
       $(function () {
       $('#datetimepicker6').datetimepicker();
       $('#datetimepicker7').datetimepicker({
        useCurrent: false //Important! See issue #1075
        });
            $("#datetimepicker6").on("dp.change", function (e) {
                $('#datetimepicker7').data("DateTimePicker").minDate(e.date);
            });
            $("#datetimepicker7").on("dp.change", function (e) {
                $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
            });
        });
    let calendario = $("<div class='input-group pull-right' id='default-daterange' style='width: 220px;padding-right: 10px;'><input type='text' name='default-daterange' class='form-control' value='' placeholder='Seleccione las fechas'><span class='input-group-append'><span class='input-group-text'><i class='fa fa-calendar'></i></span></span></div>");

    $("div.btn-dt").append('<div class="btn btn-default pull-right buscarAgente" style="margin-right: 10px;padding: 3px;padding-right: 8px;padding-left: 8px;"><i class="fas fa-search"></i> Buscar</div>').append('<div class="btn btn-primary pull-left descargarExcel" style="margin-right: 10px;padding: 3px;padding-right: 8px;padding-left: 8px;"><i class="fas fa-download"></i> Descarga Aqui</div>').append(calendario);
    $("div.btn-dt").addClass('pull-right');
    let fecha_creacion = "";
    let fecha_lanzamiento = "";
    let nombre_prueba ="";
    let numero_escenarios ="";
    let nombre_ejecutor ="";
    let nombre_matriz ="";
    let idejecucion_v1 = '';
    let resultadoSalida_temp = '';
    let resultadoFinal_temp = '';
    let resultadosMalos = 0;
    let resultadosBuenos = 0;
    let resultadosPendientes = 0;
     $(document).ready(function() {
        listarReporte_pruebas();
    });
    function changeColor(){
        $("#color-change").css("background-color","green");
    }
    function generatePDF(){
        console.log("idEjecucion: ",idejecucion_v1);
        console.log(url_pdf+'/'+idejecucion_v1+'.pdf');
        // console.log(window.open());
        let idejecucion ='';
        // window.open('/'+idejecucion_v1+'.pdf');

        var doc = new jsPDF('p', 'pt', 'letter','x','y');  
        var htmlstring = '';  
        var tempVarToCheckPageHeight = 0;  
        var pageHeight = 0;  
        var ar = "test"
        pageHeight = doc.internal.pageSize.height;  
        specialElementHandlers = {  
            // element with id of "bypass" - jQuery style selector  
            '#bypassme': function(element, renderer) {  
                // true = "handled elsewhere, bypass text extraction"  
                return true  
            }  
        };  
        margins = {  
            top: 150,  
            bottom: 60,  
            left: 40,  
            right: 40,  
            width: 600  
        };  
        var y = 70;  
        doc.setLineWidth(2); 
        doc.setFontSize(25); 
        doc.setFontStyle('bold');
        doc.text(200, y = y + 30, "Detalle de Ejecución"); 
       
        doc.setFontSize(12); 
        doc.setFontStyle('normal');

        doc.text(40, y = y + 40, "Nombre:");  
        doc.text(200, y, nombre_prueba);  
        doc.text(40, y = y + 20, "Fecha Inicio:");  
        doc.text(200, y, fecha_creacion);  
        doc.text(40, y = y + 20, "Fecha Fin:");  
        doc.text(200, y, fecha_lanzamiento);  
        doc.text(40, y = y + 20, "Usuario ejecutor:");  
        doc.text(200, y, nombre_ejecutor);  
        doc.text(40, y = y + 20, "Matriz aplicada:");  
        doc.text(200, y, nombre_matriz);  
        doc.text(40, y = y + 20, "Cantidad de Escenarios:");  
        doc.text(200, y, numero_escenarios.toString());  

        doc.autoTable({  
            html: '#detalle_prueba',  
            startY: 260,  
            theme: '',  
            columnStyles: {  
                0: {  
                    cellWidth: 70,  
                    
                },  
                1: {  
                    cellWidth: 70,  
                },  
                2: {  
                    cellWidth: 60,  
                }, 
                3: {  
                    cellWidth: 55,  
                },  
                4: {  
                    cellWidth: 60,  
                }, 
                5: {  
                    cellWidth: 55,  
                },
                6: {  
                    cellWidth: 60,  
                },
                // 7: {  
                //     cellWidth: 40,  
                // },
                7: {  
                    cellWidth: 60,  
                }  
            },  
            styles: {  
                minCellHeight: 30  
            }  
        })  
        doc.save('Reporte_Prueba.pdf');  

    }
    function otroajax(id){
        console.log("idEjecucion: ",id);
        let idejecucion ='';

        $.ajax({
            url: url_pruebas+"/"+id,
            method: 'get',
            dataType: 'json',
            async: false,
            success: function (data){
                // console.log("id prueba ajax url_pruebas ",id);
                // console.log("id ejecu url_pruebas ",data.prueba.ejecuciones[0].id_ejecucion);
                idejecucion = data.prueba.ejecuciones[0] ? data.prueba.ejecuciones[0].id_ejecucion:'';


            }
        });
        return idejecucion;
    }
     function listarReporte_pruebas() {
        $('#tabla_pruebas').DataTable().clear().destroy();
        var table = $("#tabla_pruebas").DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            lengthChange: false,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'
            },
            buttons: [
                {
       
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> Exportar PDF',
                    className: 'btn btn-danger',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                }
            ],
            initComplete: function() {
                setTimeout(function() {
                    table.buttons().container().appendTo('#tabla_pruebas_wrapper .col-md-6:eq(0)');
                }, 10);
            },
          
          
            ajax: {
                url: url_pruebas,
                dataSrc: "pruebas"
            },
            columns: [{
                    data: "id_prueba"
                },
                {
                    data: "nombre"
                },
                {
                    data: "matriz.nombre"
                },
                {
                    data: "correo"
                },
                {
                    render: function ( data, type, row ) {
                        return  row.fecha_lanzamiento+" "+row.hora_lanzamiento;
                    }
                },
                {
                    data: "tipo_lanzamiento",
                    render: function (date, type, row) {
                        let tipo_lanzamiento = row.tipo_lanzamiento;
                        let is_success_danger = tipo_lanzamiento == 'Programado' ? 'success' : 'info';
                        return '<h6><span class="badge badge-'+is_success_danger+'">'+tipo_lanzamiento+'</span></h6>';
                    }
                },
                {
                    data: "programacion",
                    render: function ( data, type, row ) {
                        return  data == null || data == 'U' ? 'Único' : 'Concurrente';
                    }
                },
                {
                    data: "tipo",
                    render: function ( data, type, row ) {
                        return  data == 'I' ? 'Interno' : 'Externo';
                    }
                },
                {
                    data: "ejecuciones",
                    render: function ( data, type, row ) {
                        // console.log("estado eje: ",data);
                        let result = '';
                        let valor = '';
                        if (row.ejecuciones.length == 0) {
                            console.log(" **** ***** es 0 ****" );
                            return  '<h6><span class="badge badge-'+"danger"+'">'+'No ejecuciones'+'</span></h6>';
                        }
                        for (const ejecucion  in row.ejecuciones) {
                            console.log("row eje: ",row.ejecuciones[ejecucion].estado);
                            if (row.ejecuciones[ejecucion].estado !== 'FINALIZADO') {
                                result = 'secondary';
                                valor = row.ejecuciones[ejecucion].estado;
                                break;
                            }
                            
                            else{
                                result =  'success';
                                valor = row.ejecuciones[ejecucion].estado;
                            }
                       
                        }
                        
                        
                        return  '<h6><span class="badge badge-'+result+'">'+valor+'</span></h6>';
                    }
                },
                {
                    render: function(data, type, row) {    
                        let id_prueba;
                        // if (row.ejecuciones.length == 0) {
                        //     console.log(" **** ***** es 0 ****" );
                        //     return '<button class="btn btn-info btn-sm" onClick="abrirDetalle(' + '' + ')"><i class="fas fa-info"></i> Detalle</button>';
                        // }
                        for (const ejecucion  in row.ejecuciones) {
                              id_prueba = row.ejecuciones[ejecucion].estado === 'FINALIZADO' ? row.id_prueba:'';
                        }   
                        return '<button class="btn btn-info btn-sm" onClick="abrirDetalle(' + id_prueba + ')"><i class="fas fa-info"></i> Detalle</button>';
                    },
                    targets: 5
                }
            ],
            order: [
                [0, 'desc']
            ]
        });
    }


     function abrirDetalle(id_prueba) {


        let idejecucion = id_prueba  ? otroajax(id_prueba):'';
        $('#detalle_prueba').DataTable().clear().destroy();
        if (idejecucion) {
            idejecucion_v1 = idejecucion;
            console.log(" detalle id_prueba: ",url_ejecuciones+"/"+idejecucion);
            let cont=0;
            resultadosMalos = 0;
            resultadosBuenos = 0;
            resultadosPendientes = 0;
            $.ajax({
                url: url_ejecuciones+"/"+idejecucion,
                method: 'get',
                dataType: 'json',
                success: function (data){
                    // console.log("data ajax ",data.ejecucion.escenarios);

                    nombre_prueba = data.ejecucion.prueba.nombre;

                    fecha_lanzamiento = data.ejecucion.prueba.fecha_lanzamiento+' '+data.ejecucion.prueba.hora_lanzamiento;
                    fecha_creacion = data.ejecucion.fecha_inicio;
                    numero_escenarios = data.ejecucion.escenarios.length;
                    nombre_ejecutor = data.ejecucion.prueba.usuario.nombres +" "+ data.ejecucion.prueba.usuario.apellidos;
                    nombre_matriz = data.ejecucion.prueba.matriz.nombre;
                    const titulo_modal = ''+''+ nombre_prueba+" / "+"  ID-"+idejecucion;
                    $('#titulo_modal').text(titulo_modal);
                    // console.log("data nombre_prueba ",nombre_prueba);
                    // console.log("data fecha_lanzamiento ",fecha_lanzamiento);
                    // console.log("data numero_escenarios ",data.ejecucion.escenarios.length);

                    $('#detalle_prueba').DataTable( {
                        dom: 'Bfrtip',  
                        buttons: [
                            'csv'
                        ],   
                        data: data.ejecucion.escenarios,                         
                        columns: [
                                    {
                                        "data": "canal_origen",
                                        render: function(data, type, row) {     
                                        cont++;              
                                        return 'Canal '+data.id_canal +' - '+data.tecnologia_operador.tecnologia.nombre +' - '+data.tecnologia_operador.operador.nombre+' - '+data.numero;
                                        }
                                    },
                                    {
                                        // "data": "destino",
                                        render: function(data, type, row) {
                                            if (row.destino != null) {
                                            return 'Canal '+row.destino.id_canal +'  '+row.destino.tecnologia_operador.tecnologia.nombre+' - '+row.destino.tecnologia_operador.operador.nombre+' - '+row.destino.numero;   
                                            }
                                            else if (row.numero_externo != null) {
                                            return 'Número Externo '+row.numero_externo.id_numero_externo +' - '+row.numero_externo.nombre+' - '+row.numero_externo.numero;                              
                                            }
                                        }
                                    },
                                    {
                                        // Resultado Salida
                                        render: function(data, type, row) {
                                            let is_success_danger = row.estado === "Success" ? 'success' : row.estado === "PENDIENTE" ? 'secondary':'danger';
                                            let is_error_succes = row.estado === "Success" ? 'Exito' : row.estado === "PENDIENTE" ? 'Pendiente': 'Error' ;
                                            // resultadoSalida_temp = is_error_succes;
                                            return '<h6><span class="badge badge-'+is_success_danger+'">'+is_error_succes+'</span></h6>';
                                        }
                                    },
                                    {
                                        "data": "hora_saliente"
                                    },
                                    {
                                        "data": "hangupReason",
                                        render: function(data, type, row) {     
                                            let hang = JSON.stringify(data);
                                            let cause = hang.split("\"")[4].replace(/\\/g, '');         
                                            let description = hang.split("\"")[8].replace(/\\/g, '');      
                                            console.log(hang.split("\"")[4].replace(/\\/g, ''));
                                            console.log(hang.split("\"")[8].replace(/\\/g, ''));
                                            return /*"Causa: "+cause +" "+ "Descripción: "+*/ description;
                                        }
                                    },
                                    {
                                        //Resultado Entrada
                                        render: function(data, type, row) {
                                            let horas_ = row.hora_saliente != null ? row.hora_saliente.split(":") : "";
                                                let times = new Date();
                                                let ti = times.toTimeString();
                                                ti = ti.split(' ')[0];
                                                ti = ti.split(":")
                                                var secondsA = (+horas_[0]) * 60 * 60 + (+horas_[1]) * 60 + (+horas_[2]); 
                                                var secondsB = (+ti[0]) * 60 * 60 + (+ti[1]) * 60 + (+ti[2]); 
                                                let diff = Math.abs(secondsA - secondsB);
                                                console.log("diferencia", diff);
                                            if (row.destino != null) {

                                                if (row.hora_entrante == null && diff <= 20) {
                                                    resultadoEntrada_temp = 'PENDIENTE';
                                                    return '<h6><span class="badge badge-'+'secondary'+'">'+"PENDIENTE"+'</span></h6>';
                                                }
                                                else if (row.hora_entrante == null && diff > 20) {
                                                    resultadoEntrada_temp = 'Error';
                                                    return '<h6><span class="badge badge-'+'danger'+'">'+"Error"+'</span></h6>';
                                                }
                                                else if (row.hora_entrante != null) {
                                                    resultadoEntrada_temp = 'Success';
                                                    return '<h6><span class="badge badge-'+'success'+'">'+"Exito"+'</span></h6>';
                                                }                                          
                                                console.log("diferencia: ", diff);
                                                return diff;
                                            }
                                            else if (row.numero_externo != null) {
                                            return "-";                              
                                            }
                                        }
                                    },
                                    {
                                        // "data": "hora_entrante"
                                        render: function(data, type, row) {
                                            // console.log("hora", row.hora_entrante.split(":"));
                                            if (row.destino != null) {
                                                if (row.hora_entrante ==null) {
                                                    return "-"
                                                }
                                                else/* if (row.hora_entrante != null)*/{
                                                    return row.hora_entrante;
                                                }
                                            }
                                            else if (row.numero_externo != null) {
                                                return "-";
                                            }
                                        }
        
                                    },
                                    {
                                        // Resultado Final
                                        render: function(data, type, row) {
                                            if (row.destino != null) {
                                                if (row.estado === "PENDIENTE" || resultadoEntrada_temp === "PENDIENTE") {
                                                    resultadoFinal_temp = 'PENDIENTE';
                                                    resultadosPendientes++;
                                                    return '<h6><span class="badge badge-'+'secondary'+'">'+"PENDIENTE"+'</span></h6>';
                                                }
                                                else if (row.estado === "Failure" || resultadoEntrada_temp === "Error") {
                                                    resultadoFinal_temp = 'Error';
                                                    console.log("malo ",resultadosMalos);
                                                    resultadosMalos ++;
                                                    return '<h6><span class="badge badge-'+'danger'+'">'+"Error"+'</span></h6>';
                                                }
                                                else if (row.estado === "Success" && resultadoEntrada_temp === "Success") {
                                                    resultadoFinal_temp = 'Success';
                                                    resultadosBuenos++;
                                                    return '<h6><span class="badge badge-'+'success'+'">'+"Exito"+'</span></h6>';
                                                }
                                    
                                            }
                                            else if (row.numero_externo != null) {
                                                // let is_success_danger = row.estado === "Success" ? 'success' : row.estado === "PENDIENTE" ? 'secondary':'danger';
                                                // resultadoFinal_temp = row.estado;
                                                // let is_error_succes = row.estado === "Success" ? 'Exito' : row.estado === "PENDIENTE" ? 'Pendiente': 'Error' ;
                                                // return '<h6><span class="badge badge-'+is_success_danger+'">'+is_error_succes+'</span></h6>';
                                                if (row.estado === "PENDIENTE" ) {
                                                    resultadoFinal_temp = 'PENDIENTE';
                                                    resultadosPendientes++;
                                                    return '<h6><span class="badge badge-'+'secondary'+'">'+"PENDIENTE"+'</span></h6>';
                                                }
                                                else if (row.estado === "Failure") {
                                                    resultadoFinal_temp = 'Error';
                                                    console.log("malo ",resultadosMalos);
                                                    resultadosMalos ++;
                                                    return '<h6><span class="badge badge-'+'danger'+'">'+"Error"+'</span></h6>';
                                                }
                                                else if (row.estado === "Success" ) {
                                                    resultadoFinal_temp = 'Success';
                                                    resultadosBuenos++;
                                                    return '<h6><span class="badge badge-'+'success'+'">'+"Exito"+'</span></h6>';
                                                }
                                            }
                                        }
                                    },
                                    {
                                        //MOS
                                        "data": "estado",
                                        render: function(data, type, row) {
                                            if (resultadoFinal_temp==="Success") {
                                                return '5';
                                            }
                                            else{
                                                return '-';
                                            }
                                            // let MOS = (row.uniqueid_en != null &&  row.mos == null) ? '5' : null;
                                            // console.log("MOS: ", MOS);
                                            // return MOS ; 
                                        }
                                    },
                                ]
                    }); 
                    console.log("resultado error: ",resultadosMalos);
                    console.log("resultado buenos: ",resultadosBuenos);
                    console.log("resultado pendientes: ",resultadosPendientes);
                    console.log("resultado cont: ",cont);
                    
                    $("#grafico").html('<div id="donut-chart" style="width:450px; height:250px"></div>');
                    var donutData = [
                        {
                            label: 'Exitosos',
                            data :resultadosBuenos,
                            color: '#00a800'
                        },
                        {
                            label: 'Fallidos',
                            data : resultadosMalos,
                            color: '#ed2939'
                        },
                        {
                            label: 'Pendientes',
                            data : resultadosPendientes,
                            color: '#7d7f7d'
                        }
                    ]
                    $.plot('#donut-chart', donutData, {
                        series: {
                            pie: {
                                    show       : true,
                                    radius     : 1,
                                    innerRadius: 0.5,
                                    label      : {
                                    show     : true,
                                    radius   : 2 / 3,
                                    formatter: labelFormatter,
                                    threshold: 0.1
                                }            
                            }
                        },
                        legend: {
                            show: false
                        }
                        
                    })

                }
            });

        }
        else{
            console.log("no hay id prueba");
            const titulo_modal = "Aun Ejecutando Pruebas";
            $('#titulo_modal').text(titulo_modal);
            $("#grafico").html('<div id="donut-chart" style="width:450px; height:250px"></div>');
                    var donutData = [
                        {
                            label: '',
                            data :1,
                            color: '#00a800'
                        },
                        {
                            label: '',
                            data : 1,
                            color: '#ed2939'
                        },
                        {
                            label: '',
                            data : 1,
                            color: '#7d7f7d'
                        },
                        {
                            label: '',
                            data : 1,
                            color: '#fff'
                        }
                    ]
                    $.plot('#donut-chart', donutData, {
                        series: {
                            pie: {
                                    show       : true,
                                    radius     : 1,
                                    innerRadius: 0.5,
                                    label      : {
                                    show     : true,
                                    radius   : 2 / 3,
                                    formatter: labelFormatter,
                                    threshold: 0.1
                                }            
                            }
                        },
                        legend: {
                            show: false
                        }
                        
                    })
                    let $elie = $("#grafico"), degree = 0, timer;
                    rotate();
                    function rotate() {
                        
                        $elie.css({ WebkitTransform: 'rotate(' + degree + 'deg)'});  
                        $elie.css({ '-moz-transform': 'rotate(' + degree + 'deg)'});                     
                        timer = setTimeout(function() {
                            ++degree; rotate();
                        },5);
                    }
        }
        $("#modal-detalle").modal("show");

        // let idejecucion = id_prueba+46;
        // console.log("idejecucion",idejecucion);


    

    }
    /*
    * Custom Label formatter
    * ----------------------
    */
    function labelFormatter(label, series) {
        return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
        + label
        + '<br>'
        + Math.round(series.percent) + '%</div>'
    }
    /*
        var table = $("#tabla_pruebas").DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            lengthChange: false,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'
            },
            buttons: ['excel', 'pdf'],
            initComplete: function() {
                setTimeout(function() {
                    table.buttons().container().appendTo('#tabla_pruebas_wrapper .col-md-6:eq(0)');
                }, 10);
            }
        });
        var table2 = $("#tabla_pruebas").DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            lengthChange: false,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'
            }
        });
        table.buttons().container().appendTo('#tabla_pruebas_wrapper .col-md-6:eq(0)');

        function abrirDetalle() {
            $('#modal-default').modal('show');
        }
        */
</script>
@endsection
