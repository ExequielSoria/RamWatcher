<?php

require_once __DIR__ . '/../controllers/ApiController.php';

// Clase encargada de gestionar

class Routes{
    
    public function index(){

        // Instancio el controlador de la api
        $controller = new ApiController();

        // Extraigo la url
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH );

        // Separo la url en segmentos
        $segments = explode('/', trim($url, '/'));

        // Obtengo el metodo HTTP
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        // Verifico el "/api"
        if (($segments[0] ?? '') !== 'api') {
            return;
        }
  
        // Identifico el recurso que pide el usuario y su ID
        $resource = $segments[1] ?? '';
        $id = $segments[2] ?? null;

        // Aca decido que hacer en cada caso de la ruta en la api, es decir los ENDPOINTS
        switch ($resource) {

            // Caso: ".../api/price/1"
            // Producto individual e historial completo de precios
            case 'price':
                
                if ( $method === 'GET' && $id !== null && ctype_digit($id) && (int) $id > 0 ){
                    $controller->showPrice($id);
                    return;
                }

                //Imprimo en formato json el error ocurrido
                http_response_code(400);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'error' => 'Petición de precio inválida'
                ]);
                return;

                http_response_code(400);
                echo 'Petición de precio inválida';
                return;


            // Caso ".../api/products"
            // Todos los productos guardados en la BDD, sin precios.                
            case 'products':

                if ($method !== 'GET') {
                    http_response_code(405);
                    header('Content-Type: application/json; charset=utf-8');
                    header('Allow: GET');

                    echo json_encode([
                        'error' => 'Método HTTP no permitido'
                    ]);

                    return;
                }

                if (count($segments) === 2) {
                    $controller->showAllProducts();
                    return;
                }

                // Caso ".../api/products/prices"
                // Aca devuelvo todos los productos de la bdd, pero con su ultimo precio registrado
                if (
                    count($segments) === 3 &&
                    $segments[2] === 'prices'
                ) {
                    $controller->showAllProductsWithLatestPrice();
                    return;
                }

                //Imprimo el error en formato json
                http_response_code(404);
                header('Content-Type: application/json; charset=utf-8');

                echo json_encode([
                    'error' => 'Ruta de productos inexistente'
                ]);

                return;



            // Si ningun caso se cumple imprimo el error en json
            default:
                http_response_code(404);
                echo 'Ruta de API inexistente';
                return;
        }
    }
}
