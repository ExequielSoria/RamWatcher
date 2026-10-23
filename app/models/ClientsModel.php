<?php

require_once __DIR__ . "/Database.php";

class ClientsModel{

    public function createClient($name, $tokenHash){

        $connection = new Connection();
        $db = $connection->connect();

        $sql = "INSERT INTO clients (name, token_hash)
                VALUES (:name, :token_hash)";

        $stmt = $db->prepare($sql);

        $stmt->execute([
            ':name' => $name,
            ':token_hash' => $tokenHash
        ]);

        return $db->lastInsertId();
    }
}
