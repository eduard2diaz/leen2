var condicion_educativa_alumno = function () {
    var tableCondicionEducativa = null;
    var objetoCondicionEducativa = null;

    var configurarDataTableCEA = function () {
        tableCondicionEducativa = $('table#condicion_educativa_alumno_entity_table').DataTable({
            "pagingType": "simple_numbers",
            "language": {
                url: datatable_url,
                "sEmptyTable":     "No hay condiciones educativas de alumnos que listar",
            },
            columns: [
                {data: 'numero'},
                {data: 'ccts'},
                {data: 'numalumnas'},
                {data: 'numalumnos'},
                {data: 'grado'},
                {data: 'acciones'}
            ]
        });
    }

    var configurarFormularioCEA = function () {
        $('select#condicion_educativa_alumnos_escuela').select2({
            dropdownParent: $("#basicmodal"),
        });
        $('select#condicion_educativa_alumnos_grado').select2({
            dropdownParent: $("#basicmodal"),
        });
        $("body div#basicmodal form[name='condicion_educativa_alumnos']").validate({
            rules: {
                'condicion_educativa_alumnos[numalumnos]': {required: true},
                'condicion_educativa_alumnos[numalumnas]': {required: true},
                'condicion_educativa_alumnos[escuela]': {required: true},
                'condicion_educativa_alumnos[grado]': {required: true},
            }
        });
    }

    var edicionCEA = function () {
        $('body').on('click', 'a.edicion_condicion_educativa_alumno', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            objetoCondicionEducativa = $(this);
            $.ajax({
                type: 'get',
                dataType: 'html',
                url: link,
                beforeSend: function (data) {
                    $.blockUI({ message: '<small>Cargando...</small>' });
                },
                success: function (data) {
                    if ($('div#basicmodal').html(data)) {
                        configurarFormularioCEA();
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

    var newCEAAction = function () {
        $('div#basicmodal').on('submit', 'form#condicion_educativa_alumno_new', function (evento) {
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
                        configurarFormularioCEA();
                    } else {
                        if (data['mensaje'])
                            toastr.success(data['mensaje']);

                        $('div#basicmodal').modal('hide');
                        var pagina = tableCondicionEducativa.page();
                        condicion_educativa_alumnoCounter++;
                        objetoCondicionEducativa = tableCondicionEducativa.row.add({
                            "numero": condicion_educativa_alumnoCounter,
                            "ccts": data['ccts'],
                            "numalumnas": data['numalumnas'],
                            "numalumnos": data['numalumnos'],
                            "grado": data['grado'],
                            "acciones": "<ul class='hidden_element list-inline pull-right'>" +
                                "<li class='list-inline-item'>" +
                                "<a class='btn btn-primary btn-sm edicion_condicion_educativa_alumno' data-href=" + Routing.generate('condicion_educativa_alumno_edit', {id: data['id']}) + "><i class='fa fa-edit'></i>Editar</a></li>" +
                                "</ul>",
                        });
                        objetoCondicionEducativa.draw();
                        tableCondicionEducativa.page(pagina).draw('page');
                    }
                },
                error: function () {
                    //base.Error();
                }
            });

        });
    }

    var escuelaCEAListener = function () {
        $('div#basicmodal').on('change', 'select#condicion_educativa_alumnos_escuela', function (evento) {
            if ($(this).val() > 0)
                $.ajax({
                    type: 'get',
                    dataType: 'json',
                    url: Routing.generate('grado_ensenanza_find_by_escuela', {'id': $(this).val()}),
                    beforeSend: function (data) {
                        $.blockUI({message: '<small>Cargando...</small>'});
                    },
                    success: function (data) {
                        if (data['empty']) {
                            toastr.error("No han sido captados los grados de enseñanza de esta escuela");
                        }
                        else
                        {
                        var cadena = "";
                        var array = JSON.parse(data);
                        if (data != null) {
                            for (var i = 0; i < array.length; i++)
                                cadena += "<option value=" + array[i]['id'] + ">" + array[i]['nombre'] + "</option>";
                        }
                        $('select#condicion_educativa_alumnos_grado').html(cadena);
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

    var edicionCEAAction = function () {
        $('div#basicmodal').on('submit', 'form#condicion_educativa_alumno_edit', function (evento) {
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
                    if (data['error']){
                        padre.html(data['form']);
                        configurarFormularioCEA();
                    }
                    else {
                        if (data['mensaje'])
                            toastr.success(data['mensaje']);

                        $('div#basicmodal').modal('hide');
                        var pagina = tableCondicionEducativa.page();
                        objetoCondicionEducativa.parents('tr').children('td:nth-child(2)').html(data['ccts']);
                        objetoCondicionEducativa.parents('tr').children('td:nth-child(3)').html(data['numalumnas']);
                        objetoCondicionEducativa.parents('tr').children('td:nth-child(4)').html(data['numalumnos']);
                        objetoCondicionEducativa.parents('tr').children('td:nth-child(5)').html(data['grado']);
                    }
                },
                error: function () {
                    //base.Error();
                }
            });
        });
    }

    var eliminarCEA = function () {
        $('div#basicmodal').on('click', 'a.eliminar_condicion_educativa_alumno', function (evento) {
            evento.preventDefault();
            var link = $(this).attr('data-href');
            var token = $(this).attr('data-csrf');
            $('div#basicmodal').modal('hide');

            bootbox.confirm({
                title: 'Eliminar condición educativa alumno',
                message: '¿Está seguro que desea eliminar esta condición educativa alumno?',
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
                                tableCondicionEducativa.row(objetoCondicionEducativa.parents('tr'))
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
                    configurarDataTableCEA();
                    edicionCEA();
                    newCEAAction();
                    escuelaCEAListener();
                    edicionCEAAction();
                    eliminarCEA();
                }
            );
        }
    }
}();