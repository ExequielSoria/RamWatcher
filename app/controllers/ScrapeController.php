<?php

// Clase encargada de las operaciones relacionadas al scrapeo del proyecto

class ScrapeController {
    //Scrapea la web y devuelve 4 valores
    //$product, $price, $model, $img y tambien el link del scrapeo para guardarlo
    function scrapeMexx($url) {
        //Url de prueba
        //$url = "https://www.mexx.com.ar/productos-rubro/memorias-ram/40388-memoria-ram-ddr4-8gb-3200-mhz-kingston-fury-beast.html";

        $html = file_get_contents($url);

        // Preparo el uso del DOM para especificar las rutas de los elementos que deseo obtener
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $link = $url;

        // Apunto al XPath de los elementos
        $nameNode = $xpath->query("//h1[contains(@class, 'title')]");
        $priceNode = $xpath->query("//h2[contains(@class, 'main-price')]/b[2]");
        $modelNode = $xpath->query("//p[.//b[contains(normalize-space(.), 'P/N')]]");
        $imgNode = $xpath->query("//img[contains(@class, 'w-100')]");

        // Compruebo qué consultas no encontraron los elementos
        $missing = [];

        if ($nameNode === false || $nameNode->length === 0) {
            $missing[] = 'name: h1.title';
        }

        if ($priceNode === false || $priceNode->length === 0) {
            $missing[] = 'price: h2.main-price b[2]';
        }

        if ($modelNode === false || $modelNode->length === 0) {
            $missing[] = 'model: p con P/N';
        }

        if ($imgNode === false || $imgNode->length === 0) {
            $missing[] = 'img: img.w-100';
        }

        if (!empty($missing)) {
            throw new RuntimeException(
                'Selectores no encontrados: ' . implode(', ', $missing)
            );
        }

        $name = trim($nameNode->item(0)->textContent);
        $price = trim($priceNode->item(0)->textContent);

        // Busco el número de parte (P/N), que identifica el modelo
        $modelElement = $modelNode->item(0);
        $modelText = $modelElement->textContent;

        if (!preg_match('/P\/N\s*:?\s*(.+)/iu', $modelText, $matches)) {
            throw new RuntimeException('No se pudo extraer el número de parte del producto');
        }

        $model = trim($matches[1]);

        $img = $imgNode->item(0)->getAttribute("src");

        if ($name === '' || $price === '' || $model === '' || $img === '') {
            throw new RuntimeException(
                'Uno o más datos del producto están vacíos'
            );
        }

        // Resultado
        $data = [
            "name" => $name,
            "price" => $price,
            "model" => $model,
            "img" => $img,
            "link" => $link
        ];

        return $data;
    }
}
