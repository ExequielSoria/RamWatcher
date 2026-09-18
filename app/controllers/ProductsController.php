<?php

require_once __DIR__ . "/../models/ProductsModel.php";

require_once __DIR__ . "/ScrapeController.php";


class ProductsController{

  function getAllProducts(){
    
    $ProductsModel = new ProductsModel();

    return $product = $ProductsModel->getAllProducts();
  }


  function showProduct( $productID ){

    $ProductsModel = new ProductsModel();



    $product = $ProductsModel->getProduct($productID);

    if ($product === false) {
        
      $product = $ProductsModel->CountProducts();
      if( $productID >  $product){


        echo '<meta http-equiv="refresh" content="0;url=/1">';
        exit;


      }

    }

    $prices = $ProductsModel->getPriceHistory($productID);


    return [$product , $prices];

  }


  //Extrae la info de la bdd, como el link y la tienda (para determinar que metodo de scrapeo usar)
  function updateProductFromDB(){}
  
  //Seguir esta estructura dentro de este controlador, me va a facilitar en el futuro poder añadir nuevos productos desde una API
  
  //Funcion pensada para ser activada mediante un script  de forma diaria
  function updateAllProducts(){

    //Consigo el id 

    
    $ProductsModel = new ProductsModel();
    $ScrapeController = new ScrapeController();

    $products = $ProductsModel->getAllProducts();

    foreach ($products as $product) {

        $productID = $product["product_id"];
        $link = $product["link"];

        $data = $ScrapeController->scrapeMexx($link);

        $price = $data["price"];

        $price = str_replace(["$", "."], "", $price);
        $price = (int) $price;

        $ProductsModel->createPrice($productID, $price);
    }



  }

  
  
    //Funcion que captura todos los datos del scrapeo y los guarda en la base de datos
    function checkProductMexx($url){
      
      //scrapear, sacar el modelo, comprobar si existe, crea el producto y añade el precio al historial

      $ScrapeController = new ScrapeController();
      $data = $ScrapeController->scrapeMexx($url);

      $ProductsModel = new ProductsModel();
      
      //si el modelo no existe, lo creamos
      if(!($ProductsModel->productExist($data['model']) ) ){
        
        echo "EL MODELO " . $data['model'] . "  NO Existe  <br>";

        $name = $data['name'];
        $model = $data['model'];
        $img = $data['img'];
        $link = $data['link'];


        $price = $data['price'];

        $price = str_replace(["$", "."], "", $price);

        $price = (int) $price;



        $newProductID = $ProductsModel->createProduct($name,$model,$img, $link);

        $ProductsModel->createPrice($newProductID, $price);


      } else {

        echo"EL PRODUCTO YA EXISTE";

        $model = $data['model'];

        $productID = $ProductsModel->getProductByModel($model);

        $price = $data['price'];

        $price = str_replace(["$", "."], "", $price);

        $price = (int) $price;


        $ProductsModel->createPrice( $productID, $price );

      }

  

    }
    
}



$ClassTesting = new ProductsController();


$url = "https://www.mexx.com.ar/productos-rubro/memorias-ram/40388-memoria-ram-ddr4-8gb-3200-mhz-kingston-fury-beast.html";

//$url = "https://www.mexx.com.ar/productos-rubro/memorias-ram/50228-memoria-ram-ddr5-32gb-6000-mhz-kingston-fury-beast-rgb.html";

//print_r($ClassTesting->updateAllProducts() );







/*
  function updateAllPrices(){
    * 
  
  }

function getProduct(){
   *trae toda la info del producto de la Base de datos y su ultimo precio registrado*
  }

  function getProductPriceHistory($id_product){
  *trae el historial de precios de un producto*
  }

  **/