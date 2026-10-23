<?php

require_once __DIR__ . '/../models/ProductsModel.php';
require_once __DIR__ . '/../models/ClientsModel.php';
require_once __DIR__ . "/ProductsController.php";


class ApiController{

    public function showAllProducts() {

        header('Content-Type: application/json; charset=utf-8');
    
        try {

            $model = new ProductsModel();
            $products = $model->getAllProducts();
    
            http_response_code(200);
    
            echo json_encode( ['products' => $products], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

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

            echo json_encode( ['products' => $products], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );


        } catch (Throwable $e) {

            error_log( 'Error en API showAllProductsWithLatestPrice: ' . $e->getMessage() );

            http_response_code(500);

            echo json_encode([ 'error' => 'Error interno al consultar los productos y sus precios' ]);
        }
    }


    public function showPrice($id){

        header('Content-Type: application/json; charset=utf-8');

        // 1. Validar el ID recibido
        if ( !is_scalar($id) || !ctype_digit((string) $id) || (int) $id <= 0 ) {
            
            http_response_code(400);

            echo json_encode( ['error' => 'El ID del producto debe ser un entero positivo'] );

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

                echo json_encode( ['error' => 'Producto no encontrado', 'id' => $id] );

                return;
            }

            $prices = $model->getPriceHistory($id);

            // 4. Responder con los datos obtenidos
            http_response_code(200);

            echo json_encode( ['product' => $product,'prices' => $prices], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

        } catch (Throwable $e) {
            // Registrar el error real en los logs, sin exponerlo al cliente
            error_log('Error en API showPrice: ' . $e->getMessage());

            http_response_code(500);

            echo json_encode( ['error' => 'Error interno al consultar el producto'] );
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

    public function createProduct() {

        header('Content-Type: application/json; charset=utf-8');

        try {

            $data = json_decode(file_get_contents('php://input'), true);

            if (!is_array($data) || !isset($data['url']) || !is_string($data['url'])) {
                http_response_code(400);
                echo json_encode([
                    'error' => 'Debés enviar una URL en formato JSON'
                ]);
                return;
            }

            $url = trim($data['url']);

            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                http_response_code(400);
                echo json_encode([
                    'error' => 'La URL no es válida'
                ]);
                return;
            }

            $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');

            if (!in_array($host, ['mexx.com.ar', 'www.mexx.com.ar'], true)) {
                http_response_code(400);
                echo json_encode([
                    'error' => 'Por ahora solo se admiten URLs de Mexx'
                ]);
                return;
            }

            $productsController = new ProductsController();
            $result = $productsController->checkProductMexx($url);

            http_response_code($result['created'] ? 201 : 200);

            echo json_encode( ['message' => $result['created'] ? 'Producto agregado correctamente' : 'El producto ya existía; se registró un nuevo precio','product' => $result ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

        } catch (Throwable $e) {
            error_log('Error en API createProduct: ' . $e->getMessage());

            http_response_code(500);
            echo json_encode( ['error' => 'No se pudo procesar el producto'], JSON_UNESCAPED_UNICODE );
        }

    }


    public function authenticateClient(){

        header('Content-Type: application/json; charset=utf-8');

        // Obtener el encabezado Authorization.
        $authorization = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? '';

        // Aceptar únicamente tokens Bearer de 64 caracteres hexadecimales.
        if (
            !is_string($authorization) ||
            !preg_match('/^Bearer\s+([a-f0-9]{64})$/i', trim($authorization), $matches)
        ) {
            http_response_code(401);
            header('WWW-Authenticate: Bearer');

            echo json_encode([
                'error' => 'Token ausente o inválido'
            ]);

            return false;
        }

        try {
            $token = $matches[1];
            $tokenHash = hash('sha256', $token);

            $model = new ClientsModel();
            $client = $model->getClientByTokenHash($tokenHash);

            if ($client === false) {
                http_response_code(401);
                header('WWW-Authenticate: Bearer');

                echo json_encode([
                    'error' => 'Token inválido o cliente inexistente'
                ]);

                return false;
            }

            return true;

        } catch (Throwable $e) { 
            
            error_log('Error en API authenticateClient: ' . $e->getMessage());

            http_response_code(500);

            echo json_encode([
                'error' => 'Error interno al validar el token'
            ]);

            return false;
        }
    }


    public function deleteProduct($id) {
        
        header('Content-Type: application/json; charset=utf-8');

        if (!is_scalar($id) || !ctype_digit((string) $id) || (int) $id <= 0) { 
            http_response_code(400);
            echo json_encode( ['error' => 'El ID del producto debe ser un entero positivo'] );
        return;
        }

        $id = (int) $id;

        try {
            $model = new ProductsModel();
            $deleted = $model->deleteProduct($id);

            if ($deleted === false) {
                http_response_code(404);
                echo json_encode([
                    'error' => 'Producto no encontrado',
                    'id' => $id
                ]);
                return;
            }

            http_response_code(200);
            echo json_encode( ['message' => 'Producto eliminado correctamente', 'id' => $id], JSON_UNESCAPED_UNICODE );

        } catch (Throwable $e) {

            error_log('Error en API deleteProduct: ' . $e->getMessage());

            http_response_code(500);
            echo json_encode( ['error' => 'Error interno al eliminar el producto'] );
        }
    }

    public function deleteProductPrices($id) {
        header('Content-Type: application/json; charset=utf-8');

        if (!is_scalar($id) || !ctype_digit((string) $id) || (int) $id <= 0) {
            
            http_response_code(400);
            echo json_encode( ['error' => 'El ID del producto debe ser un entero positivo'] );

            return;
        }

        $id = (int) $id;

        try {

            $model = new ProductsModel();
            $product = $model->getProduct($id);

            if ($product === false) {
                http_response_code(404);
                echo json_encode( ['error' => 'Producto no encontrado','id' => $id] );
                return;
            }

            $deletedCount = $model->deletePriceHistory($id);

            http_response_code(200);
            echo json_encode([
                'message' => 'Historial de precios eliminado correctamente',
                'product_id' => $id,
                'deleted_records' => $deletedCount
            ], JSON_UNESCAPED_UNICODE);

        } catch (Throwable $e) {
            error_log('Error en API deleteProductPrices: ' . $e->getMessage());

            http_response_code(500);
            echo json_encode( ['error' => 'Error interno al eliminar el historial de precios'] );
        }

    }



}
