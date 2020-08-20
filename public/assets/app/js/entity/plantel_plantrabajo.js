var plantel_plantrabajo = function () {
    var table = null;
    var obj = null;

    var configurarDataTable = function () {
        table = $('table#plantrabajo_entity_table').DataTable({
            "pagingType": "simple_numbers",
            "language": {
                url: datatable_url,
                "sEmptyTable": "No hay planes de trabajo que listar",
            },
            columns: [
                {data: 'id'},
                {data: 'numero'},
                {data: 'fechainicio'},
                {data: 'acciones'}
            ]
        });
    }

    function previewfile(evt) {
        var f = evt.target.files[0]; // FileList object
        var size=f.size;
        var name=f.name;
        $('button#file_chooser').attr('data-original-title',name);
        $('button#file_chooser').attr('title',name);
        $('button#file_chooser i').attr('class','fa fa-file');
    }

    var escucharArchivo = function () {
        $('div#basicmodal').on('click', 'button#file_chooser', function (evento) {
            $('input#plan_trabajo_file').click();
        });
        document.getElementById('plan_trabajo_file').addEventListener('change', previewfile, false);
    }

    var configurarFormulario = function () {
        jQuery.validator.addMethod("greaterThan",
            function (value, element, params) {
                fechaf = moment(value);
                fechai = moment($(params).val())
                if (fechaf.isValid() && fechai.isValid())
                    return fechaf > fechai;
                else
                    return true;
            }, 'Tiene que ser mayor  que la fecha de inicio');

        $('select#plan_trabajo_tipoAccion').select2({
            dropdownParent: $("#basicmodal"),
        });
        $('input#plan_trabajo_fechainicio').datepicker();
        $('input#plan_trabajo_fechafin').datepicker();
        $("body div#basicmodal form[name='plan_trabajo']").validate({
            rules: {
                'plan_trabajo[tipoAccion]': {required: true},
                'plan_trabajo[fechainicio]': {required: true},
                'plan_trabajo[fechafin]': {greaterThan: "#plan_trabajo_fechainicio"},
                'plan_trabajo[tiempoestimado]': {required: true},
                'plan_trabajo[costoestimado]': {required: true},
                'plan_trabajo[montoasignado]': {required: true},
                'plan_trabajo[descripcionaccion]': {required: true},
            }
        });
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
                    $.blockUI({message: '<small>Cargando...</small>'});
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

    var newAction = function () {
        $('div#basicmodal').on('submit', 'form#plantrabajo_new', function (evento) {
            evento.preventDefault();
            var padre = $(this).parent();
            var l = Ladda.create(document.querySelector('.ladda-button'));
            $.ajax({
                url: $(this).attr("action"),
                type: "POST",
                data: new FormData(this), //para enviar el formulario hay que serializarlo
                contentType: false,
                cache: false,
                processData: false,
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
                    } else {
                        if (data['mensaje'])
                            toastr.success(data['mensaje']);

                        $('div#basicmodal').modal('hide');
                        var pagina = table.page();
                        plantrabajoCounter++;
                        objeto = table.row.add({
                            "id": plantrabajoCounter,
                            "numero": data['numero'],
                            "fechainicio": data['fechainicio'],
                            "acciones": "<ul class='hidden_element list-inline pull-right'>" +
                                "<li class='list-inline-item'>" +
                                "<a class='btn btn-sm' href=" + Routing.generate('plan_trabajo_show', {id: data['id']}) + "><i class='fa fa-eye'></i>Visualizar</a></li>" +
                                "<li class='list-inline-item'>" +
                                "<a class='btn btn-primary btn-sm edicion' data-href=" + Routing.generate('plan_trabajo_edit', {id: data['id']}) + "><i class='fa fa-edit'></i>Editar</a></li>" +
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

    var edicionAction = function () {
        $('div#basicmodal').on('submit', 'form#plan_trabajo_edit', function (evento) {
            evento.preventDefault();
            var padre = $(this).parent();
            var l = Ladda.create(document.querySelector('.ladda-button'));
            $.ajax({
                url: $(this).attr("action"),
                type: "POST",
                data: $(this).serialize(),
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
                    } else {
                        if (data['mensaje'])
                            toastr.success(data['mensaje']);

                        $('div#basicmodal').modal('hide');
                        var pagina = table.page();
                        obj.parents('tr').children('td:nth-child(2)').html(data['numero']);
                        obj.parents('tr').children('td:nth-child(3)').html(data['fechainicio']);
                    }

                },
                error: function () {
                    //base.Error();
                }
            });
        });
    }

    var eliminar = function () {
        $('div#basicmodal').on('click', 'a.eliminar_plan_trabajo', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            var token = $(this).attr('data-csrf');
            $('div#basicmodal').modal('hide');

            bootbox.confirm({
                title: 'Eliminar plan de trabajo',
                message: '¿Está seguro que desea eliminar este plan de trabajo?',
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
                                $.blockUI({message: '<h1><img src="busy.gif" /> Just a moment...</h1>'});
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
        init: function () {
            $().ready(function () {
                    configurarDataTable();
                    newAction();
                    edicionAction();
                    edicion();
                    eliminar();
                }
            );
        }
    }
}();