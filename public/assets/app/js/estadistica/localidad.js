var localidad = function () {

    var configurarFormulario = function () {
        $('select#estadistica_localidad_estado').select2();
        $('select#estadistica_localidad_municipio').select2({
            allowClear: true
        });
        $('select#estadistica_localidad_ciudad').select2({
            allowClear: true
        });
    }

    var estadoListener = function () {
        $('body').on('change', 'select#estadistica_localidad_estado', function (evento)
        {
            if ($(this).val() > 0)
                $.ajax({
                    type: 'get', //Se uso get pues segun los desarrolladores de yahoo es una mejoria en el rendimineto de las peticiones ajax
                    dataType: 'json',
                    url: Routing.generate('municipio_find_by_estado', {'id': $(this).val()}),
                    beforeSend: function (data) {
                        $.blockUI({message: '<small>Cargando...</small>'});
                    },
                    success: function (data) {
                        var cadena="<option value=''> </option>";
                        var array=JSON.parse(data);
                        if(data!=null) {
                            for (var i = 0; i < array.length; i++)
                                cadena+="<option value="+array[i]['id']+">"+array[i]['nombre']+"</option>";
                            $('select#estadistica_localidad_municipio').html(cadena);
                        }
                        else{
                            $('select#estadistica_localidad_municipio').html(cadena);
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


    return {
        init: function () {
            $().ready(function () {
                    configurarFormulario();
                    estadoListener();
                }
            );
        }
    }
}();
