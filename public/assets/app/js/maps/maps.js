var maps = function () {
    var mapa;
    var popupGlobal = new ol.Overlay({
        element: document.getElementById('popup')
        //autoPan: true,
    });

    var configurarFormulario = function () {
        $('select#maps_estado').select2({
            placeholder: "Seleccione un estado"
        });
        $('select#maps_municipio').select2({
            placeholder: "Seleccione un municipio",
            allowClear: true
        });
    }

    var estadoListener = function () {
        $('body').on('change', 'select#maps_estado', function (evento) {

            if ($(this).val() != "") {
                $.ajax({
                    type: 'get',
                    dataType: 'json',
                    url: Routing.generate('maps_municipio_findby_estado', {'estado': $(this).val()}),
                    beforeSend: function () {
                        $.blockUI({message: '<small>Cargando...</small>'});
                    },
                    success: function (data) {
                        var cadena = "";
                        var array = JSON.parse(data);
                        if (data != null) {
                            for (var i = 0; i < array.length; i++)
                                cadena += "<option value='" + array[i]['nombre'] + "'>" + array[i]['nombre'] + "</option>";
                            console.log(cadena);
                        }
                        $('select#maps_municipio').html(cadena);
                    },
                    error: function () {
                        //base.Error();
                    },
                    complete: function () {
                        $.unblockUI();
                    }
                });
            }

        });
    }

    //-------------------------Inicio de los Estilos ------

    var colores = ['#00A8C6', '#C7F464', '#FF6B6B', '#C44D58', '#D1F2A5', '#EFFAB4', '#FFC48C', '#FF9F80', '#F56991,', '#4ECDC4', '#40C0CB', "#8FBE00"];

    var selectedStyle2 = new ol.style.Style({
        image: new ol.style.Circle({
            radius: 15,
            fill: new ol.style.Fill({
                color: 'rgba(150,150,200,0.6)'
            }),
            stroke: new ol.style.Stroke({
                color: 'rgba(20,30,100,0.8)',
                width: 3
            })
        })
    });

    var selectedStyle = new ol.style.Style({
        fill: new ol.style.Fill({
            color: 'rgba(255, 100, 50, 0.3)'
        }),
        stroke: new ol.style.Stroke({
            width: 2,
            color: 'rgba(255, 100, 50, 0.8)'
        }),
        image: new ol.style.Circle({
            fill: new ol.style.Fill({
                color: 'rgba(255, 100, 50, 0.5)'
            }),
            stroke: new ol.style.Stroke({
                width: 2,
                color: 'rgba(255, 100, 50, 0.8)'
            }),
            radius: 10
        })
    });

    var selectedTextStyleFunction = function (name) {
        return new ol.style.Style({
            text: new ol.style.Text({
                font: '14px helvetica,sans-serif',
                text: name,
                fill: new ol.style.Fill({
                    color: '#000'
                }),
                stroke: new ol.style.Stroke({
                    color: '#fff',
                    width: 2
                })
            })
        });
    };
    //-------------------------Fin de los Estilos ------


    var crearMapa = function () {
        //Se crea una capa de tipo Raster(osm significa que el provedor es OpenStreetMaster)
        var capa_osm = new ol.layer.Tile({
            title: "OSM",
            name: "osm",
            type: 'base',
            visible: true,
            source: new ol.source.OSM()
        });

        mapa = new ol.Map({
            target: 'map',
            crossOriginKeyword: null,
            layers: [new ol.layer.Group({
                'title': 'Base maps',
                layers: [capa_osm]
            }),
            ],
            view: new ol.View({
                center: ol.proj.transform([-99.41, 25], 'EPSG:4326', 'EPSG:3857'),
                zoom: 5
            })
        });
        //Añadiendo una barra de desplazamiento al zoom
        mapa.addControl(new ol.control.ZoomSlider());
        mapa.addOverlay(popupGlobal);
    }

    var cargandoEscuelas = function () {
        $('body').on('submit', 'form#maps_form', function (evento) {
            evento.preventDefault();
            $.ajax({
                url: $(this).attr("action"),
                type: "POST",
                dataType: 'json',
                data: $(this).serialize(),
                beforeSend: function () {
                    $.blockUI({message: '<h1><img src="busy.gif" /> Just a moment...</h1>'});
                },
                complete: function () {
                    $.unblockUI();
                },
                success: function (response) {
                    mi_json = JSON.parse(response);
                    if (mi_json.features == null)
                        toastr.error("No se encontraron resultados");
                    else {
                        var source = new ol.source.Vector({
                            loader: function (extent, resolution, projection) {
                                var format = new ol.format.GeoJSON();

                                var features = format.readFeatures(response,
                                    {featureProjection: projection});
                                source.addFeatures(features);
                            },
                            strategy: ol.loadingstrategy.bbox
                        });

                        var geojson_vectorLayer = new ol.layer.Vector({
                            source: source,
                        });
                        mapa.addLayer(geojson_vectorLayer);
                    }
                },
                error: function () {
                    //base.Error();
                }
            });
        });
    }

    //Almacena todos los puntos de coordenadas que han sido seleccionados
    var selectedFeatures = [];

    //Limpia todos los puntos de coordenadas que han sido seleccionados
    function unselectPreviousFeatures() {
        var i;
        for (i = 0; i < selectedFeatures.length; i++) {
            selectedFeatures[i].setStyle(null);
        }
        selectedFeatures = [];
    }

    //Escucha cuando se seleccione un nuevo punto de coordenada
    var escuchandoEventos = function () {
        //mapa.on('singleclick', function (evt) {
        mapa.on('pointermove', function (evt) {
            unselectPreviousFeatures();
            /*
              Tomo el valor actual del popup y lo oculto, a fin de que no se quede marcado si
              ya salio del elemento
             */
            var element = popupGlobal.getElement();
            $(element).popover('dispose');//Es el destroy de otras versiones de popover

            var feature = mapa.forEachFeatureAtPixel(evt.pixel, function (feature, layer) {
                // Aquí se puede filtrar la feature
                return feature;
            });

            //Si selecciona un marcador
            if (feature) {
                var cctt = feature.get('cctt');
                var coordinate = evt.coordinate;

                $.ajax({
                    url: Routing.generate('maps_findBycctt'),
                    type: "POST",
                    data: {"cctt": cctt},
                    beforeSend: function () {
                    },
                    complete: function () {
                    },
                    success: function (response) {
                        feature.setStyle([
                            selectedStyle
                        ]);
                        selectedFeatures.push(feature);

                        //Muevo el popup a las coordenadas del nuevo marcador
                        popupGlobal.setPosition(coordinate);
                        $(element).popover({
                            'placement': 'top',
                            'animation': false,
                            'html': true,
                            'content': response
                        });
                        //Muestro el popup
                        $(element).popover('show');
                    },
                    error: function () {
                        //base.Error();
                    }
                });
            }
        });
    }

    return {
        init: function () {
            $().ready(function () {
                    configurarFormulario();
                    crearMapa();
                    cargandoEscuelas();
                    escuchandoEventos();
                    estadoListener();
                }
            );
        }
    }
}();
