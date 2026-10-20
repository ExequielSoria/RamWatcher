<?php

class Connection
{
    function connect()
    {
        $host = getenv("MYSQL_HOST") ?: ($_ENV["MYSQL_HOST"] ?? "");
        $database = getenv("DATABASE_NAME") ?: ($_ENV["DATABASE_NAME"] ?? "");
        $user = getenv("MYSQL_USER") ?: ($_ENV["MYSQL_USER"] ?? "");
        $pass = getenv("MYSQL_PASSWORD");

        if ($pass === false) {
            $pass = $_ENV["MYSQL_PASSWORD"] ?? "";
        }

        if ($host === "" || $database === "" || $user === "") {
            throw new RuntimeException("Faltan variables de entorno para MySQL.");
        }

        $dsn = "mysql:host={$host};dbname={$database}";
        $link = new PDO($dsn, $user, $pass);
        $link->exec("SET NAMES utf8");

        return $link;
    }
}