<?php

require_once __DIR__ . "/../models/ProductsModel.php";
require_once __DIR__ . "/ScrapeController.php";


class ProductsController{

    function getAllProducts(){
      
      $ProductsModel = new ProductsModel();
      return $product = $ProductsModel->getAllProducts();
    }

    //Esta es la funcion inicial del index, es la que llama y muestra el producto
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

  
  
    public function checkProductMexx($url) {

        $scrapeController = new ScrapeController();
        $data = $scrapeController->scrapeMexx($url);

        if (!is_array($data) || empty($data['model'])) {
            throw new RuntimeException('No se pudo obtener el modelo del producto');
        }

        $productsModel = new ProductsModel();
        $model = $data['model'];
        $exists = $productsModel->productExist($model);

        $price = str_replace(['$', '.', ' '], '', $data['price'] ?? '');

        if (!ctype_digit($price)) {
            throw new RuntimeException('No se pudo interpretar el precio del producto');
        }

        $price = (int) $price;

        if ($price <= 0) {
            throw new RuntimeException('El precio del producto no es válido');
        }

        if (!$exists) {
            $name = $data['name'];
            $img = $data['img'];
            $link = $data['link'];

            $newProductID = $productsModel->createProduct(
                $name,
                $model,
                $img,
                $link
            );

            $productsModel->createPrice($newProductID, $price);

            return [
                'created' => true,
                'product_id' => $newProductID,
                'model' => $model
            ];
        }

        $productID = $productsModel->getProductByModel($model);
        $productsModel->createPrice($productID, $price);

        return [
            'created' => false,
            'product_id' => $productID,
            'model' => $model
        ];
      }


    public function getAdjacentProductId($id, $direction) {
        $model = new ProductsModel();
        return $model->getAdjacentProductId($id, $direction);
    }

    
}