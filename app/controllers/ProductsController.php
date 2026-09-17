<?php

include_once("../models/ProductsModel.php");

include_once("./ScrapeController.php");


class ProductsController{

  //Extrae la info de la bdd, como el link y la tienda (para determinar que metodo de scrapeo usar)
  function updateProductFromDB(){}
  
  //Seguir esta estructura dentro de este controlador, me va a facilitar en el futuro poder añadir nuevos productos desde una API
  
  //Funcion pensada para ser activada mediante un script  de forma diaria
  //
  function updateAllProducts(){}

  



  
    //Funcion que captura todos los datos del scrapeo y los guarda en la base de datos
    function checkProduct($url){
  
  
      $ScrapeController = new ScrapeController();

      print_r( $ScrapeController->scrapeMexx($url) );
  
    }
    
}



$ClassTesting = new productController();

$url = "https://www.mexx.com.ar/productos-rubro/memorias-ram/40388-memoria-ram-ddr4-8gb-3200-mhz-kingston-fury-beast.html";

print_r( $ClassTesting->checkProduct($url)  );







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