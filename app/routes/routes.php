<?php


//Enrutator
class Routes
{

    public function index()
    {
        // Extraigo la url
        $url = parse_url(
            $_SERVER['REQUEST_URI'],
            PHP_URL_PATH
        );

        // Separo la url en segmentos
        $segments = explode('/', trim($url, '/'));

        // Obtengo el metodo http
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        // Verifico que la url pida a la api
        if (($segments[0] ?? '') !== 'api') {
            return;
        }

        // Identifico el recurso que pide el usuario y su ID
        $resource = $segments[1] ?? '';
        $id = $segments[2] ?? null;

        // Interpreto la ruta
        switch ($resource) {
            case 'price':

                if ($method === 'GET' && $id !== null) {
                    // Próximo paso: llamar al controlador
                    echo "Ruta reconocida: consultar precio del producto " . htmlspecialchars($id);
                    return;
                }

                http_response_code(400);
                echo 'Petición de precio inválida';
                return;

            default:
                http_response_code(404);
                echo 'Ruta de API inexistente';
                return;
        }
    }
}
