<!DOCTYPE html>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API docs</title>
    <link rel="stylesheet" href="/style-docs.css">

    <!-- Fuentes de Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap"
          rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&family=Rubik+Pixels&display=swap"
          rel="stylesheet">


</head>
<body class="docs">
    

<main>
    <h1 >Ram Observer API</h1>


    <p><a href="/">Volver a Ram Observer</a></p>


    <p>
        Documentación de la API de Ram Watcher.
        Permite consultar productos, obtener historiales de precios,
        agregar productos y administrar sus registros.
    </p>

    <p>
        Todas las rutas utilizan el prefijo <code>/api</code>.
        Las respuestas de la API utilizan formato JSON, excepto esta
        página de documentación.
    </p>

    <p>
        Los endpoints protegidos requieren un token Bearer.
        Los identificadores de productos deben ser enteros positivos.
    </p>

    <h2 class="highlight" >Índice</h2>

    <ul>
        <li><a href="#get-products">Consultar productos</a></li>
        <li><a href="#get-products-prices">Consultar productos con precios</a></li>
        <li><a href="#get-price">Consultar historial de precios</a></li>
        <li><a href="#post-products">Agregar un producto</a></li>
        <li><a href="#delete-product">Eliminar un producto</a></li>
        <li><a href="#delete-prices">Eliminar historial de precios</a></li>
        <li><a href="#post-clients">Crear un cliente</a></li>
        <li><a href="#authentication">Autenticación</a></li>
        <li><a href="#status-codes">Códigos HTTP</a></li>
        <li><a href="#examples">Ejemplos con cURL</a></li>
    </ul>

    <h2 class="highlight" id="get-products">1. Consultar todos los productos</h2>

    <p><strong>Método:</strong> GET</p>
    <p><strong>Ruta:</strong> <code>/api/products</code></p>
    <p><strong>Autenticación:</strong> No requerida.</p>

    <p>
        Obtiene la lista de productos registrados en Ram Watcher.
    </p>

    <h3>Respuesta exitosa: 200 OK</h3>

    <pre>{
    "products": [
        {
            "product_id": 1,
            "link": "https://www.mexx.com.ar/..."
        }
    ]
}</pre>

    <p>
        El objeto de ejemplo es ilustrativo. Los campos devueltos
        dependen de la consulta utilizada por el modelo.
    </p>

    <h3>Errores posibles</h3>

    <ul>
        <li>500: error interno al consultar los productos.</li>
    </ul>

    <h2 class="highlight" id="get-products-prices">2. Consultar productos con sus últimos precios</h2>

    <p><strong>Método:</strong> GET</p>
    <p><strong>Ruta:</strong> <code>/api/products/prices</code></p>
    <p><strong>Autenticación:</strong> No requerida.</p>

    <p>
        Obtiene los productos registrados junto con el precio más
        reciente y la fecha de ese registro.
    </p>

    <h3>Respuesta exitosa: 200 OK</h3>

    <pre>{
    "products": [
        {
            "product_id": 1,
            "name": "Nombre del producto",
            "model": "MODELO",
            "latest_price": "213319.00",
            "latest_price_date": "2026-10-23 16:00:00"
        }
    ]
}</pre>

    <p>
        Los campos <code>latest_price</code> y
        <code>latest_price_date</code> corresponden al último precio
        registrado. Pueden ser null si el producto no tiene historial.
    </p>

    <h3>Errores posibles</h3>

    <ul>
        <li>500: error interno al consultar los productos y sus precios.</li>
    </ul>

    <h2 class="highlight" id="get-price">3. Consultar un producto y su historial de precios</h2>

    <p><strong>Método:</strong> GET</p>
    <p><strong>Ruta:</strong> <code>/api/price/{id}</code></p>
    <p><strong>Autenticación:</strong> No requerida.</p>

    <p>
        Devuelve los datos de un producto y todos sus registros históricos
        de precios, ordenados por fecha ascendente.
    </p>

    <h3>Parámetros de ruta</h3>

    <ul>
        <li><code>id</code>: ID entero positivo del producto.</li>
    </ul>

    <h3>Ejemplo de petición</h3>

    <pre>GET /api/price/1</pre>

    <h3>Respuesta exitosa: 200 OK</h3>

    <pre>{
    "product": {
        "product_id": 1,
        "name": "Nombre del producto",
        "model": "MODELO",
        "url_img": "https://www.mexx.com.ar/...",
        "link": "https://www.mexx.com.ar/..."
    },
    "prices": [
        {
            "price": "205000.00",
            "created_at": "2026-10-20 16:00:00"
        },
        {
            "price": "213319.00",
            "created_at": "2026-10-23 16:00:00"
        }
    ]
}</pre>

    <h3>Errores posibles</h3>

    <ul>
        <li>400: el ID no es un entero positivo.</li>
        <li>404: el producto no existe.</li>
        <li>500: error interno al consultar el producto.</li>
    </ul>

    <h2 class="highlight" id="post-products">4. Agregar un producto</h2>

    <p><strong>Método:</strong> POST</p>
    <p><strong>Ruta:</strong> <code>/api/products</code></p>
    <p><strong>Autenticación:</strong> Requerida.</p>

    <p>
        Agrega un producto a partir de su URL de Mexx. El servidor utiliza
        el scraper para obtener los datos del producto.
    </p>

    <p>
        Si el producto ya existe, se registra un nuevo precio en lugar
        de crear otro producto.
    </p>

    <h3>Cuerpo de la petición</h3>

    <p>Content-Type: application/json</p>

    <pre>{
    "url": "https://www.mexx.com.ar/..."
}</pre>

    <h3>Campos requeridos</h3>

    <ul>
        <li><code>url</code>: URL válida de un producto de Mexx.</li>
    </ul>

    <h3>Respuesta: 201 Created</h3>

    <p>Se creó un producto nuevo.</p>

    <pre>{
    "message": "Producto agregado correctamente",
    "product": {
        "created": true
    }
}</pre>

    <h3>Respuesta: 200 OK</h3>

    <p>El producto ya existía y se registró un nuevo precio.</p>

    <pre>{
    "message": "El producto ya existía; se registró un nuevo precio",
    "product": {
        "created": false
    }
}</pre>

    <p>
        Los objetos de ejemplo están abreviados. El campo
        <code>product</code> contiene el resultado real devuelto
        por el controlador de productos.
    </p>

    <h3>Errores posibles</h3>

    <ul>
        <li>400: falta la URL, no es válida o no pertenece a un dominio permitido.</li>
        <li>401: el token está ausente o es inválido.</li>
        <li>500: no se pudo procesar el producto.</li>
    </ul>

    <p>
        Actualmente se admiten URLs de los dominios
        <code>mexx.com.ar</code> y <code>www.mexx.com.ar</code>.
        El scraper también debe poder identificar los datos del producto.
    </p>

    <h2 class="highlight" id="delete-product">5. Eliminar un producto</h2>

    <p><strong>Método:</strong> DELETE</p>
    <p><strong>Ruta:</strong> <code>/api/products/{id}</code></p>
    <p><strong>Autenticación:</strong> Requerida.</p>

    <p>
        Elimina un producto y todos sus registros históricos de precios.
    </p>

    <h3>Parámetros de ruta</h3>

    <ul>
        <li><code>id</code>: ID entero positivo del producto que se desea eliminar.</li>
    </ul>

    <h3>Ejemplo de petición</h3>

    <pre>DELETE /api/products/1
Authorization: Bearer TU_TOKEN</pre>

    <h3>Respuesta exitosa: 200 OK</h3>

    <pre>{
    "message": "Producto eliminado correctamente",
    "id": 1
}</pre>

    <h3>Errores posibles</h3>

    <ul>
        <li>400: ID inválido.</li>
        <li>401: el token está ausente o es inválido.</li>
        <li>404: el producto no existe.</li>
        <li>500: error interno al eliminar el producto.</li>
    </ul>

    <p>
        Esta operación elimina tanto el producto como su historial.
        No se puede deshacer mediante este endpoint.
    </p>

    <h2 class="highlight" id="delete-prices">6. Eliminar el historial de precios</h2>

    <p><strong>Método:</strong> DELETE</p>
    <p><strong>Ruta:</strong> <code>/api/products/{id}/prices</code></p>
    <p><strong>Autenticación:</strong> Requerida.</p>

    <p>
        Elimina todos los registros históricos de precios de un producto,
        pero conserva el producto en la base de datos.
    </p>

    <h3>Parámetros de ruta</h3>

    <ul>
        <li><code>id</code>: ID entero positivo del producto.</li>
    </ul>

    <h3>Ejemplo de petición</h3>

    <pre>DELETE /api/products/1/prices
Authorization: Bearer TU_TOKEN</pre>

    <h3>Respuesta exitosa: 200 OK</h3>

    <pre>{
    "message": "Historial de precios eliminado correctamente",
    "product_id": 1,
    "deleted_records": 20
}</pre>

    <p>
        <code>deleted_records</code> indica cuántos registros fueron
        eliminados. Puede ser cero si el producto no tenía historial.
    </p>

    <h3>Errores posibles</h3>

    <ul>
        <li>400: ID inválido.</li>
        <li>401: el token está ausente o es inválido.</li>
        <li>404: el producto no existe.</li>
        <li>500: error interno al eliminar el historial.</li>
    </ul>

    <h2 class="highlight" id="post-clients">7. Crear un cliente y generar un token</h2>

    <p><strong>Método:</strong> POST</p>
    <p><strong>Ruta:</strong> <code>/api/clients</code></p>
    <p><strong>Autenticación:</strong> No requerida.</p>

    <p>
        Registra un cliente y genera un token aleatorio para autenticar
        sus futuras peticiones.
    </p>

    <h3>Cuerpo de la petición</h3>

    <p>Content-Type: application/json</p>

    <pre>{
    "name": "Mi aplicación"
}</pre>

    <h3>Campos requeridos</h3>

    <ul>
        <li>
            <code>name</code>: nombre de entre 1 y 100 caracteres,
            sin contar los espacios externos eliminados.
        </li>
    </ul>

    <h3>Respuesta exitosa: 201 Created</h3>

    <pre>{
    "message": "Cliente creado correctamente",
    "client": {
        "id": 1,
        "name": "Mi aplicación"
    },
    "token": "TOKEN_GENERADO"
}</pre>

    <p>
        El token contiene 64 caracteres hexadecimales. La base de datos
        almacena su hash SHA-256, no el token original.
    </p>

    <p>
        Guardá el token de forma segura al recibirlo y no lo publiques
        ni lo incluyas en repositorios.
    </p>

    <h3>Errores posibles</h3>

    <ul>
        <li>400: el JSON o el nombre son inválidos.</li>
        <li>500: error interno al crear el cliente.</li>
    </ul>

    <h2 class="highlight" id="authentication">8. Autenticación</h2>

    <p>
        Los endpoints protegidos requieren un token válido en el encabezado
        HTTP <code>Authorization</code>.
    </p>

    <p>Formato del encabezado:</p>

    <pre>Authorization: Bearer TU_TOKEN</pre>

    <p>Endpoints que requieren autenticación:</p>

    <ul>
        <li><code>POST /api/products</code></li>
        <li><code>DELETE /api/products/{id}</code></li>
        <li><code>DELETE /api/products/{id}/prices</code></li>
    </ul>

    <p>
        Si el token no se envía o no es válido, la API responde con
        <code>401 Unauthorized</code>.
    </p>

    <p>
        El endpoint <code>POST /api/clients</code> no requiere
        autenticación. Si la API se expone públicamente, conviene
        limitar quién puede registrar nuevos clientes.
    </p>

    <h2 class="highlight" id="status-codes">9. Códigos HTTP</h2>

    <ul>
        <li><code>200 OK</code>: la operación se completó correctamente.</li>
        <li><code>201 Created</code>: se creó un producto o cliente.</li>
        <li><code>400 Bad Request</code>: los datos enviados son inválidos.</li>
        <li><code>401 Unauthorized</code>: falta el token o es inválido.</li>
        <li><code>404 Not Found</code>: el recurso o la ruta no existe.</li>
        <li><code>405 Method Not Allowed</code>: el método HTTP no está permitido.</li>
        <li><code>500 Internal Server Error</code>: ocurrió un error interno.</li>
    </ul>

    <h3>Ejemplo de respuesta de error</h3>

    <pre>{
    "error": "Producto no encontrado",
    "id": 999
}</pre>

    <h2 class="highlight" id="examples">10. Ejemplos con cURL</h2>

    <h3>Consultar todos los productos</h3>

    <pre>curl -i "https://tu-dominio.com/api/products"</pre>

    <h3>Consultar productos con sus últimos precios</h3>

    <pre>curl -i "https://tu-dominio.com/api/products/prices"</pre>

    <h3>Consultar el historial de un producto</h3>

    <pre>curl -i "https://tu-dominio.com/api/price/1"</pre>

    <h3>Crear un cliente</h3>

    <pre>curl -i -X POST "https://tu-dominio.com/api/clients" \
  -H "Content-Type: application/json" \
  -d '{"name":"Mi aplicación"}'</pre>

    <h3>Agregar un producto</h3>

    <pre>curl -i -X POST "https://tu-dominio.com/api/products" \
  -H "Authorization: Bearer TU_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"url":"https://www.mexx.com.ar/..."}'</pre>

    <h3>Eliminar un producto</h3>

    <pre>curl -i -X DELETE "https://tu-dominio.com/api/products/1" \
  -H "Authorization: Bearer TU_TOKEN"</pre>

    <h3>Eliminar el historial de precios</h3>

    <pre>curl -i -X DELETE "https://tu-dominio.com/api/products/1/prices" \
  -H "Authorization: Bearer TU_TOKEN"</pre>

    <p><a href="/">Volver a Ram Observer</a></p>
</main>


</body>
</html>