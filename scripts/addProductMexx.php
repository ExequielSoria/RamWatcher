<?php

require_once __DIR__ . '/../app/controllers/ProductsController.php';

if ($argc < 2) {
    echo "Uso: php create-product.php \"URL\"\n";
    exit(1);
}

$url = $argv[1];

$ProductsController = new ProductsController();

$ProductsController->checkProductMexx($url);