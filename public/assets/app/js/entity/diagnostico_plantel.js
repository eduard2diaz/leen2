var diagnostico_plantel = function () {
    var table = null;
    var obj = null;

    var configurarDataTable = function () {
        table = $('table#diagnostico_entity_table').DataTable({
            "pagingType": "simple_numbers",
            "language": {
                url: datatable_url,
                "sEmptyTable":     "No hay diagnósticos que listar",
            },
            columns: [
                {data: 'numero'},
                {data: 'identificador'},
                {data: 'fecha'},
                {data: 'acciones'}
            ]
        });
    }

    var personalizarDataTableShow = function () {
        $('table#condicion_docente_educativa_entity_table').DataTable({
            "pagingType": "simple_numbers",
            "language": {
                url: datatable_url
            },
            columns: [
                {data: 'numero'},
                {data: 'ccts'},
                {data: 'curp'},
                {data: 'nombre'},
                {data: 'grado'}
            ]
        });

        $('table#condicion_educativa_alumno_entity_table').DataTable({
            "pagingType": "simple_numbers",
            "language": {
                url: datatable_url
            },
            columns: [
                {data: 'numero'},
                {data: 'ccts'},
                {data: 'numalumnas'},
                {data: 'numalumnos'},
                {data: 'grado'}
            ]
        });
    }

    function previewfile(evt) {
        var f = evt.target.files[0]; // FileList object
        var reader = new FileReader();
        var size=f.size;
        var name=f.name;
        $('button#file_chooser').attr('data-original-title',name);
        $('button#file_chooser').attr('title',name);
        $('span#nombre_archivo').html(name);
        $('button#file_chooser i').attr('class','fa fa-file');
    }

    var escucharArchivo = function () {
        $('body').on('click', 'button#file_chooser', function (evento) {
            $('input#diagnostico_plantel_file').click();
        });
        document.getElementById('diagnostico_plantel_file').addEventListener('change', previewfile, false);
    }

    var configurarFormulario = function () {
        $('select#diagnostico_plantel_proyecto').select2();
        $('select#diagnostico_plantel_idcondicionesAula').select2();
        $('select#diagnostico_plantel_idcondicionessanitarios').select2();
        $('select#diagnostico_plantel_idcondicionoficina').select2();
        $('select#diagnostico_plantel_idcondicionesbliblioteca').select2();
        $('select#diagnostico_plantel_idcondicionaulamedios').select2();
        $('select#diagnostico_plantel_idcondicionpatio').select2();
        $('select#diagnostico_plantel_idcondicioncanchasdeportivas').select2();
        $('select#diagnostico_plantel_idcondicionbarda').select2();
        $('select#diagnostico_plantel_idcondicionagua').select2();
        $('select#diagnostico_plantel_idcondiciondrenaje').select2();
        $('select#diagnostico_plantel_idcondicionenergia').select2();
        $('select#diagnostico_plantel_idcondiciontelefono').select2();
        $('select#diagnostico_plantel_idcondicioninternet').select2();
        $('input#diagnostico_plantel_fecha').datepicker();

        $("body form[name='diagnostico_plantel']").validate({
            rules: {
                'diagnostico_plantel[fecha]': {required: true},
                'diagnostico_plantel[numeroaulas]': {required: true},
                'diagnostico_plantel[numerosanitarios]': {required: true},
                'diagnostico_plantel[idcondicionesAula]': {required: true},
                'diagnostico_plantel[idcondicionessanitarios]': {required: true},
                'diagnostico_plantel[numerooficinas]': {required: true},
                'diagnostico_plantel[idcondicionoficina]': {required: true},
                'diagnostico_plantel[numerobibliotecas]': {required: true},
                'diagnostico_plantel[idcondicionesbliblioteca]': {required: true},
                'diagnostico_plantel[numeroaulasmedios]': {required: true},
                'diagnostico_plantel[idcondicionaulamedios]': {required: true},
                'diagnostico_plantel[numeropatio]': {required: true},
                'diagnostico_plantel[idcondicionpatio]': {required: true},
                'diagnostico_plantel[numerocanchasdeportivas]': {required: true},
                'diagnostico_plantel[numerobarda]': {required: true},
                'diagnostico_plantel[idcondicionbarda]': {required: true},
                'diagnostico_plantel[idcondicionagua]': {required: true},
                'diagnostico_plantel[idcondiciondrenaje]': {required: true},
                'diagnostico_plantel[idcondicionenergia]': {required: true},
                'diagnostico_plantel[idcondiciontelefono]': {required: true},
                'diagnostico_plantel[idcondicioninternet]': {required: true},
            }
        });
    }

    var addEditAction = function () {
        $('body').on('submit', 'form#diagnostico_plantel', function (evento) {
            evento.preventDefault();
            var padre = $(this).parent();
            var l = Ladda.create(document.querySelector('.ladda-button'));
            $.ajax({
                url: $(this).attr("action"),
                type: "POST",
                data: new FormData(this), //para enviar el formulario hay que serializarlo
                contentType: false,
                cache: false,
                processData:false,
                beforeSend: function () {
                    l.start();
                },
                complete: function () {
                    l.stop();
                },
                success: function (data) {
                    if (data['error']) {
                        padre.html(data['form']);
                        configurarFormulario();
                    }
                    else
                        document.location.href=data['url'];
                },
                error: function () {
                    //base.Error();
                }
            });
        });
    }

    var eliminar = function () {
        $('body').on('click', 'a.eliminar_diagonostico_plantel', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            var token = $(this).attr('data-csrf');
            $('div#basicmodal').modal('hide');

            bootbox.confirm({
                title: 'Eliminar diagnóstico plantel',
                message: '¿Está seguro que desea eliminar este diagnóstico plantel?',
                buttons: {
                    confirm: {
                        label: 'Si, estoy seguro',
                        className: 'btn-sm btn-primary'
                    },
                    cancel: {
                        label: 'Cancelar',
                        className: 'btn-sm btn-secondary'
                    }
                },
                callback: function (result) {
                    if (result == true)
                        $.ajax({
                            type: 'get',
                            url: link,
                            data: {
                                _token: token
                            },
                            beforeSend: function () {
                                $.blockUI({ message: '<h1><img src="busy.gif" /> Just a moment...</h1>' });
                            },
                            complete: function () {
                                $.unblockUI();
                            },
                            success: function (data) {
                                document.location.href=data['url'];
                            },
                            error: function () {
                                //base.Error();
                            }
                        });
                }
            });
        });
    }


    return {
        index: function () {
            $().ready(function () {
                    configurarDataTable();
                }
            );
        },
        addEdit: function () {
            $().ready(function () {
                addEditAction();
                configurarFormulario();
                escucharArchivo();
                }
            );
        },
        show: function () {
            $().ready(function () {
                personalizarDataTableShow();
                eliminar();
                }
            );
        }
    }
}();