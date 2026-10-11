<?php

header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'mensaje' => 'API de RamWatcher funcionando',
    'recurso' => $_GET['resource'] ?? null,
    'id' => isset($_GET['id']) ? (int) $_GET['id'] : null
]);

