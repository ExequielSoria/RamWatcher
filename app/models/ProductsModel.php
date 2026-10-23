<?php

require_once __DIR__ . "/../controllers/ProductsController.php" ;

require_once __DIR__ . "/Database.php";

class ProductsModel{
   
    public function getAllProductsWithLatestPrice(){
        $db = Connection::connect();

        $query = $db->prepare("
            SELECT
                p.*,
                ph.price AS latest_price,
                ph.created_at AS latest_price_date
            FROM products p
            LEFT JOIN price_history ph
                ON ph.product_id = p.product_id
                AND ph.created_at = (
                    SELECT MAX(ph2.created_at)
                    FROM price_history ph2
                    WHERE ph2.product_id = p.product_id
                )
            ORDER BY p.product_id ASC
        ");

        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    


    public function countProducts(){

    $db = Connection::connect();

    $query = $db->query(
        "SELECT COUNT(*) FROM products"
    );

    return $query->fetchColumn();

    }


    //Devuelve el id y el link de la web
    public function getAllProducts(){
        $db = Connection::connect();

        $query = $db->query(
            "SELECT product_id, link
            FROM products"
        );

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }



    function productExist($model){

        $db = Connection::connect();

        $query = $db->prepare(
            "SELECT 1
            FROM products
            WHERE model = :model
            LIMIT 1"
        );

        $query->execute([
            ":model" => $model
        ]);

        return $query->fetch() !== false;
    }

    function createPrice($productId, $price){

        echo "CREANDO PRECIO <br>";


        $db = Connection::connect();

        $query = $db->prepare(
            "INSERT INTO price_history (product_id, price)
            VALUES (:product_id, :price)"
        );

        $query->execute([
            ":product_id" => $productId,
            ":price" => $price
        ]);

        var_dump($query->errorInfo());

    }


    function createProduct($name, $model, $img, $link){

        echo "<br> CREANDO PRODUCTO <br>";

        $db = Connection::connect();

        $query = $db->prepare(
            "INSERT INTO products (name, model, url_img, link)
            VALUES (:name, :model, :img, :link)"
        );

        $query->execute([
            ":name" => $name,
            ":model" => $model,
            ":img" => $img,
            ":link" => $link

        ]);

        return $db->lastInsertId();
    }


    function getProductByModel($model){

        $db = Connection::connect();

        $query = $db->prepare(
            "SELECT product_id
            FROM products
            WHERE model = :model
            LIMIT 1"
        );

        $query->execute([
            ":model" => $model
        ]);

        return $query->fetchColumn();
    }



    public function getProduct($productID){
        
        $db = Connection::connect();

        $query = $db->prepare(
            "SELECT *
            FROM products
            WHERE product_id = :product_id"
        );

        $query->execute([
            ":product_id" => $productID
        ]);

        $product = $query->fetch(PDO::FETCH_ASSOC);

        if ($product === false) {
            return false;
        }

        return $product;
    }


    function getPriceHistory($productID){
        $db = Connection::connect();

        $query = $db->prepare(
            "SELECT price, created_at
            FROM price_history
            WHERE product_id = :product_id
            ORDER BY created_at ASC"
        );

        $query->execute([
            ":product_id" => $productID
        ]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


    public function deletePriceHistory($productID) {
    $db = Connection::connect();

    $query = $db->prepare("
        DELETE FROM price_history
        WHERE product_id = :product_id
    ");

    $query->execute([
        ":product_id" => $productID
    ]);

    return $query->rowCount();
}

    public function deleteProduct($productID) {
        $db = Connection::connect();

        try {
            $db->beginTransaction();

            // Primero eliminamos el historial asociado.
            $query = $db->prepare("
                DELETE FROM price_history
                WHERE product_id = :product_id
            ");

            $query->execute([
                ":product_id" => $productID
            ]);

            // Después eliminamos el producto.
            $query = $db->prepare("
                DELETE FROM products
                WHERE product_id = :product_id
            ");

            $query->execute([
                ":product_id" => $productID
            ]);

            if ($query->rowCount() === 0) {
                $db->rollBack();
                return false;
            }

            $db->commit();
            return true;

        } catch (Throwable $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }

            throw $e;
        }
    
    }


}


