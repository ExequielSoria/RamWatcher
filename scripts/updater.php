<?php

require_once __DIR__ . "/../app/controllers/ProductsController.php";

$controller = new ProductsController();

$controller->updateAllProducts();