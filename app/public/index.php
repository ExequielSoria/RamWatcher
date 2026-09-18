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

print($prices);
    


?>


    <!--- Implementacion de Google Charts --->
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script>

        google.charts.load('current', {
            packages: ['corechart'],
            language: 'es-AR'
        });




        google.charts.load('current', {
            packages: ['corechart']
        });

        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

        var data = google.visualization.arrayToDataTable([
            ['Fecha', 'Precio', { role: 'style' }],

            <?php foreach ($prices as $price): ?>
                [
                    '<?= date('d/m/Y', strtotime($price['created_at'])) ?>',
                    <?= $price['price']?>,
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
                        fontSize: 30,
                        bold: true,
                        color: '#000000ff'
                    }
                },

                backgroundColor: 'transparent',

                chartArea: {
                    left: 5,
                    top: 5,
                    right: 5,
                    bottom: 5,
                    width: '94%',
                    height: '94%'
                },


                //Si las barras son mas de X el groupWidth pasa a ser de 60%
                bar: {
                    groupWidth: groupWidth
                },

                    // Eje X
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

                // Eje Y
                vAxis: {

                    format: '$#,##0',

                    textPosition: 'none',

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

            chart.draw(data, options);
        }

    </script>

</head>
<body>
    
<p class="tittle1">RAM</p>


<p class="liña">◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼</p>
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


        <p class="product-name"><?= $product["name"] ?></p>

<a href="/<?= $id + 1 ?>">

        <input class="selector-btn" type="button" value=">">
</a>

</div>



</body>
</html>