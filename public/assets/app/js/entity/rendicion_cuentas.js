var rendicion_cuentas = function () {
    var table = null;
    var obj = null;

    var configurarDataTable = function () {
        table = $('table#rendicion_entity_table').DataTable({
            "pagingType": "simple_numbers",
            "language": {
                url: datatable_url,
                "sEmptyTable":     "No hay rendiciones de cuenta que listar",
            },
            columns: [
                {data: 'numero'},
                {data: 'tipoaccion'},
                {data: 'fecha'},
                {data: 'acciones'}
            ]
        });
    }

    var configurarFormulario = function () {
        $('select#rendicion_cuentas_tipoAccion').select2({
            dropdownParent: $("#basicmodal"),
        });
        $('input#rendicion_cuentas_fechacaptura').datepicker();
        $("body div#basicmodal form[name='rendicion_cuentas']").validate({
            rules: {
                'rendicion_cuentas[tipoAccion]': {required: true},
                'rendicion_cuentas[fechacaptura]': {required: true},
                'rendicion_cuentas[monto]': {required: true},
            }
        });
    }

    function previewfile(evt) {
        var f = evt.target.files[0]; // FileList object
        var reader = new FileReader();
        var size=f.size;
        var name=f.name;
        $('button#file_chooser').attr('data-original-title',name);
        $('button#file_chooser').attr('title',name);
        $('button#file_chooser i').attr('class','fa fa-file');
    }

    var escucharArchivo = function () {
        $('div#basicmodal').on('click', 'button#file_chooser', function (evento) {
            $('input#rendicion_cuentas_file').click();
        });
        document.getElementById('rendicion_cuentas_file').addEventListener('change', previewfile, false);
    }

    var edicion = function () {
        $('body').on('click', 'a.edicion', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            obj = $(this);
            $.ajax({
                type: 'get',
                dataType: 'html',
                url: link,
                beforeSend: function (data) {
                    $.blockUI({ message: '<small>Cargando...</small>' });
                },
                success: function (data) {
                    if ($('div#basicmodal').html(data)) {
                        $('div#basicmodal').modal('show');
                        configurarFormulario();
                        escucharArchivo();
                    }
                },
                error: function () {
                    //base.Error();
                },
                complete: function () {
                    $.unblockUI();
                }
            });
        });
    }

    var addAction = function () {
        $('body').on('submit', 'form#rendicioncuentas_new', function (evento) {
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
                    else{
                        if (data['mensaje'])
                            toastr.success(data['mensaje']);

                        $('div#basicmodal').modal('hide');
                        var pagina = table.page();
                        rendicionCuentasCounter++;
                        objeto = table.row.add({
                            "numero": rendicionCuentasCounter,
                            "tipoaccion": data['tipoaccion'],
                            "fecha": data['fecha'],
                            "acciones": "<ul class='hidden_element list-inline pull-right'>" +
                                "<li class='list-inline-item'>" +
                                "<a class='btn default btn-sm showRendicion' data-href=" + Routing.generate('rendicion_cuentas_show', {id: data['id']}) + "><i class='fa fa-eye'></i>Visualizar</a></li>" +
                                "<li class='list-inline-item'>" +
                                "<a class='btn btn-primary btn-sm edicion' data-href=" + Routing.generate('rendicion_cuentas_edit', {id: data['id']}) + "><i class='fa fa-edit'></i>Editar</a></li>" +
                                "</ul>",
                        });
                        objeto.draw();
                        table.page(pagina).draw('page');
                    }
                },
                error: function () {
                    //base.Error();
                }
            });
        });
    }

    var editAction = function () {
        $('body').on('submit', 'form#rendicion_cuentas_edit', function (evento) {
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
                    else{
                        if (data['mensaje'])
                            toastr.success(data['mensaje']);

                        $('div#basicmodal').modal('hide');
                        var pagina = table.page();
                        obj.parents('tr').children('td:nth-child(2)').html(data['tipoaccion']);
                        obj.parents('tr').children('td:nth-child(3)').html(data['fecha']);
                    }
                },
                error: function () {
                    //base.Error();
                }
            });
        });
    }

    var show = function () {
        $('body').on('click', 'a.showRendicion', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            obj = $(this);
            $.ajax({
                type: 'get',
                dataType: 'html',
                url: link,
                beforeSend: function (data) {
                    $.blockUI({ message: '<small>Cargando...</small>' });
                },
                success: function (data) {
                    if ($('div#basicmodal').html(data)) {
                        $('div#basicmodal').modal('show');
                    }
                },
                error: function () {
                    //base.Error();
                },
                complete: function () {
                    $.unblockUI();
                }
            });
        });
    }

    var eliminar = function () {
        $('div#basicmodal').on('click', 'a.eliminar_rendicion', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            var token = $(this).attr('data-csrf');
            $('div#basicmodal').modal('hide');

            bootbox.confirm({
                title: 'Eliminar rendición de cuentas',
                message: '¿Está seguro que desea eliminar esta rendición de cuentas?',
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
                                table.row(obj.parents('tr'))
                                    .remove()
                                    .draw('page');
                                toastr.success(data['mensaje']);
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
                    edicion();
                    show();
                    addAction();
                    editAction();
                    eliminar();
                }
            );
        },
    }
}();