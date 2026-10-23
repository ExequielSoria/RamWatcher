<?php

require_once __DIR__ . '/../models/ProductsModel.php';
require_once __DIR__ . '/../models/ClientsModel.php';

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


    public function showPrice($id){

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


    public function createClient(){

        header('Content-Type: application/json; charset=utf-8');

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if ( !is_array($data) || !isset($data['name']) || !is_string($data['name']) ) {

                http_response_code(400);
                echo json_encode(['error' => 'Debés enviar un nombre válido en formato JSON']);
                return;
            }

            $name = trim($data['name']);

            if ($name === '' || strlen($name) > 100) {
                http_response_code(400);
                echo json_encode([
                    'error' => 'El nombre debe tener entre 1 y 100 caracteres'
                ]);
                return;
            }

            // Generar un token aleatorio seguro.
            $token = bin2hex(random_bytes(32));

            // Guardar únicamente el hash del token.
            $tokenHash = hash('sha256', $token);

            $model = new ClientsModel();
            $clientId = $model->createClient($name, $tokenHash);

            http_response_code(201);

            echo json_encode([
                'message' => 'Cliente creado correctamente',
                'client' => [
                    'id' => $clientId,
                    'name' => $name
                ],
                'token' => $token
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        } catch (Throwable $e) {
            error_log('Error en API createClient: ' . $e->getMessage());

            http_response_code(500);
            echo json_encode([
                'error' => 'Error interno al crear el cliente'
            ]);
        }
    }


}
