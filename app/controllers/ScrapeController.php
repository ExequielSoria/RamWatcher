<?php

// Clase encargada de las operaciones relacionadas al scrapeo del proyecto

class ScrapeController{
    
    //Scrapea la web y devuelve 4 valores
    //$product, $price, $model, $img y tambien el link del scrapeo para guardarlo
    function scrapeMexx($url){ 
        
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

        // Apunto al xpath de los elementos y los guardo en un array
        $name = trim(
            $xpath->query("//h1[contains(@class, 'title')]")->item(0)->textContent
        );
        
        //Convierto el precio string a numerico
        $price = trim(
            $xpath->query("//h2[contains(@class, 'main-price')]/b[2]")->item(0)->textContent

        );
        
        $model = trim(
            $xpath->query("//p[contains(@class, 'mb-0') and b[contains(text(), 'Modelo')]]")
                ->item(0)
                ->childNodes
                ->item(1)
                ->textContent
        );
        
        $img = $xpath->query(
            "//img[contains(@class, 'w-100')]"
        )->item(0)->getAttribute("src");
        
    
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




