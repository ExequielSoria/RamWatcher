<!DOCTYPE html>
<html lang="es">
<head>

    <link rel="stylesheet" href="/style.css">

    <link rel="icon" href="/favicon.png" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ram Observer</title>

    <!-- Fuentes de Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap"
          rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&family=Rubik+Pixels&display=swap"
          rel="stylesheet">

    <?php
    
        require_once __DIR__ . "/../controllers/ProductsController.php";

        $controller = new ProductsController();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 1;

        $productData = $controller->showProduct($id);

        $product = $productData[0];
        $prices = $productData[1];

        // Valores necesarios para configurar el gráfico.
        $priceValues = array_column($prices, 'price');

        $minPrice = min($priceValues);
        $maxPrice = max($priceValues);
        
        
        
        //Valores necesarios para los botones de ANTERIOR y SIGUIENTE
        $previousId = $controller->getAdjacentProductId($id, 'previous');
        $nextId = $controller->getAdjacentProductId($id, 'next');

        $previousId = $previousId !== false ? $previousId : 1;
        $nextId = $nextId !== false ? $nextId : 1;

    ?>

    <!-- Google Charts -->
    <script src="https://www.gstatic.com/charts/loader.js"></script>

    <!-- Datos del producto para main.js -->
    <script id="chart-data" type="application/json">
        <?= json_encode(
            array_map(function ($price) {
                return [
                    'date' => date(
                        'd/m/Y',
                        strtotime($price['created_at'])
                    ),
                    'price' => (float) $price['price']
                ];
            }, $prices),
            JSON_HEX_TAG |
            JSON_HEX_APOS |
            JSON_HEX_QUOT |
            JSON_HEX_AMP
        ) ?>
    </script>

<script src="/main.js?v=2" defer></script>

</head>

<body>

<div class="tittle-container">

    <div class="btn-corner">
        <a href="/docs.php" >Docs API</a>
    </div>


    <div class="tittle-linked">
        <p class="tittle1">RAM</p>
        <p class="line">
            ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼ ◼
        </p>
        
        <p class="tittle2">Observer</p>
    </div>

    <div>
        <a href="https://www.mexx.com.ar" target="blank"  </a> <img class="logo-mexx" src="https://www.mexx.com.ar/assets/img/kit/logo_mexx_v2.png" alt="Logo de tienda mexx"> 
    </div>


</div>




    <div class="container">
        <div
            id="chart_div"
            data-min-price="<?= $minPrice * 0.9 ?>"
            data-max-price="<?= $maxPrice * 1.05 ?>"
            data-price-count="<?= count($prices) ?>"
        ></div>
    </div>

    <div class="selector">

        <div class="imagen-pixel">
            <img
                src="<?= htmlspecialchars($product['url_img'], ENT_QUOTES, 'UTF-8') ?>"
                alt="Imagen del producto"
            >
        </div>


        <a href="/<?= $previousId ?>">
            <input class="selector-btn" type="button" value="<" >
        </a>

        <a
            target="_blank"
            rel="noopener noreferrer"
            href="<?= htmlspecialchars($product['link'], ENT_QUOTES, 'UTF-8') ?>" 
        >


        <p class="product-name">
                <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
        </p>
        
    </a>


        <a href="/<?= $nextId ?>">
            <input
                class="selector-btn"
                type="button"
                value=">"
            >
        </a>

    </div>

</body>
</html>