<!DOCTYPE html>
<html lang="es">
<head>
    
    <link rel="stylesheet" href='style.css'>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ram Tracker</title>

    
    <!--- Fuentes de Google Fonts --->
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&family=Rubik+Pixels&display=swap" rel="stylesheet">


<?php

    require_once __DIR__ . "/../controllers/ProductsController.php";

    $controller = new ProductsController();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 1;

$productData = $controller->showProduct($id);

$product = $productData[0];
$prices = $productData[1];

//print($prices);
    


?>


    <!--- Implementacion de Google Charts --->
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script>





google.charts.load('current', {

    packages: ['corechart'],

    language: 'es-AR'

});

google.charts.setOnLoadCallback(drawChart);


function drawChart() {

    var data = google.visualization.arrayToDataTable([

        ['Fecha', 'Precio', { role: 'style' }],

        <?php foreach ($prices as $price): ?>

            [

                '<?= date('d/m/Y', strtotime($price['created_at'])) ?>',

                <?= $price['price'] ?>,

                '#0ABF00'

            ],

        <?php endforeach; ?>

    ]);


    var groupWidth = <?= count($prices) > 15 ? "'70%'" : "'90%'" ?>;


    var options = {

        tooltip: {

            trigger: 'focus',

            textStyle: {

                fontName: 'VT323',

                fontSize: 25,

                bold: true,

                color: '#000000ff'

            }

        },

        backgroundColor: 'transparent',


        // Sin animación de Google Charts

        animation: {

            startup: false

        },


        chartArea: {

            left: 5,

            top: 5,

            right: 5,

            bottom: 5,

            width: '94%',

            height: '94%'

        },


        bar: {

            groupWidth: groupWidth

        },


        hAxis: {

            textPosition: 'none',

            baselineColor: 'transparent',

            gridlines: {

                color: 'transparent'

            },

            minorGridlines: {

                color: 'transparent'

            }

        },


        vAxis: {

            format: '$#,##0',

            textPosition: 'none',

            viewWindow: {
                    min: <?= min(array_column($prices, 'price')) * 0.9 ?>,
                    max: <?= max(array_column($prices, 'price')) * 1.05 ?>
                },



            textStyle: {

                color: '#0ABF00',

                fontSize: 20

            },

            baselineColor: '#ff0000ff',

            gridlines: {

                color: 'none'

            },

            minorGridlines: {

                color: 'none'

            }

        },


        legend: {

            position: 'none'

        }

    };


    var chart = new google.visualization.ColumnChart(

        document.getElementById('chart_div')

    );


    // Dibujamos el gráfico UNA SOLA VEZ

    chart.draw(data, options);


    // Esperamos a que Google termine de generar el SVG

    setTimeout(function() {

        prepararAnimacion();

    }, 100);


    function prepararAnimacion() {

        var svg = document.querySelector('#chart_div svg');

        if (!svg) return;


        var rects = svg.querySelectorAll('rect');

        var barras = [];


        rects.forEach(function(rect) {

            var fill = rect.getAttribute('fill');


            if (fill === '#0ABF00') {

                var y = parseFloat(rect.getAttribute('y'));

                var height = parseFloat(rect.getAttribute('height'));


                if (!isNaN(y) && !isNaN(height) && height > 0) {

                    barras.push({

                        rect: rect,

                        y: y,

                        height: height,

                        bottom: y + height

                    });

                }

            }

        });


        if (barras.length === 0) return;


        // ==========================================
        // EMPEZAMOS OCULTANDO LAS BARRAS
        // ==========================================

        barras.forEach(function(barra) {

            barra.rect.setAttribute(

                'height',

                0

            );

            barra.rect.setAttribute(

                'y',

                barra.bottom

            );

        });


        // ==========================================
        // 1° FOTOGRAMA → 30%
        // ==========================================

        setTimeout(function() {

            cambiarBarras(barras, 0.30);

        }, 100);


        // ==========================================
        // 2° FOTOGRAMA → 60%
        // ==========================================

        setTimeout(function() {

            cambiarBarras(barras, 0.60);

        }, 1100);


        // ==========================================
        // 3° FOTOGRAMA → 100%
        // ==========================================

        setTimeout(function() {

            cambiarBarras(barras, 1.00);

        }, 2100);

    }


    function cambiarBarras(barras, porcentaje) {

        barras.forEach(function(barra) {

            var nuevaAltura = barra.height * porcentaje;

            var nuevoY = barra.bottom - nuevaAltura;


            // Cambio INSTANTÁNEO.
            // No hay transición ni suavizado.

            barra.rect.setAttribute(

                'height',

                nuevaAltura

            );

            barra.rect.setAttribute(

                'y',

                nuevoY

            );

        });

    }

}






    </script>

    <script src="main.js"></script>

</head>
<body>

<p class="tittle1">RAM</p>


<p class="liña">◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼</p>
<!--  <hr class="line">  --->

<p class="tittle2">Watcher</p>

<div class="container">

    <div id="chart_div"></div>

</div>

<div class="selector">

<div class="imagen-pixel"><img src=" <?= $product["url_img"] ?> " alt="Imagen del producto"></div>


<a href="/<?= $id - 1 ?>">
    <input class="selector-btn" type="button" value="<">
</a>

<a target="_blank" href="<?= $product["link"] ?>">
    <p class="product-name"><?= $product["name"] ?></p>
</a>

<a href="/<?= $id + 1 ?>">
    <input class="selector-btn" type="button" value=">">
</a>



</body>
</html>