var base = function () {

    var loadsuscripcionFormAction = function () {
        $.ajax({
            url: Routing.generate('suscriptor_new'),
            type: "GET",
            success: function (data) {
                $('div#suscripcion_form').html(data['html'])
            },
            error: function () {
                //base.Error();
            }
        });
    }

    var subscribirAction = function () {
        $('body').on('submit', 'form#suscriptor_new', function (evento) {
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
                    if (data['error'])
                        padre.html(data['form']);
                    else
                        if (data['mensaje']){
                            toastr.success(data['mensaje']);
                            $('input#suscriptor_email').val('');
                            $('div#subsciption_errors').html('');
                        }
                },
                error: function () {
                    //base.Error();
                }
            });
        });
    }

    return {
        init: function () {
            $().ready(function () {
                //subscribirAction();
     //           loadsuscripcionFormAction();
                }
            );
        }
    }
}();


