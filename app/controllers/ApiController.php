<?php

require_once __DIR__ . '/../models/ProductsModel.php';

class ApiController{

    public function showAllProducts() {
        header('Content-Type: application/json; charset=utf-8');
    
        try {
            $model = new ProductsModel();
            $products = $model->getAllProducts();
    
            http_response_code(200);
    
            echo json_encode(
                ['products' => $products],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        } catch (Throwable $e) {
            error_log('Error en API showAllProducts: ' . $e->getMessage());
    
            http_response_code(500);
    
            echo json_encode([
                'error' => 'Error interno al consultar los productos'
            ]);
        }
    }
    

    public function showAllProductsWithLatestPrice() {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $model = new ProductsModel();
            $products = $model->getAllProductsWithLatestPrice();

            http_response_code(200);

            echo json_encode(
                ['products' => $products],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        } catch (Throwable $e) {
            error_log(
                'Error en API showAllProductsWithLatestPrice: ' . $e->getMessage()
            );

            http_response_code(500);

            echo json_encode([
                'error' => 'Error interno al consultar los productos y sus precios'
            ]);
        }
    }














    public function showPrice($id)
    {
        header('Content-Type: application/json; charset=utf-8');

        // 1. Validar el ID recibido
        if (
            !is_scalar($id) ||
            !ctype_digit((string) $id) ||
            (int) $id <= 0
        ) {
            http_response_code(400);

            echo json_encode([
                'error' => 'El ID del producto debe ser un entero positivo'
            ]);

            return;
        }

        $id = (int) $id;

        try {
            // 2. Consultar el producto y su historial
            $model = new ProductsModel();

            $product = $model->getProduct($id);

            // 3. Verificar que el producto exista
            if ($product === false) {
                http_response_code(404);

                echo json_encode([
                    'error' => 'Producto no encontrado',
                    'id' => $id
                ]);

                return;
            }

            $prices = $model->getPriceHistory($id);

            // 4. Responder con los datos obtenidos
            http_response_code(200);

            echo json_encode([
                'product' => $product,
                'prices' => $prices
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        } catch (Throwable $e) {
            // Registrar el error real en los logs, sin exponerlo al cliente
            error_log('Error en API showPrice: ' . $e->getMessage());

            http_response_code(500);

            echo json_encode([
                'error' => 'Error interno al consultar el producto'
            ]);
        }
    }
}
