<?php
// Router para servidor PHP integrado
// Maneja health checks y sirve archivos estáticos

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Health check para Railway (solo /health)
if ($uri === '/health') {
    http_response_code(200);
    header('Content-Type: text/plain');
    echo 'OK';
    exit;
}

// Redirigir raíz a login
if ($uri === '/') {
    header('Location: /login.php');
    exit;
}

// Servir archivos estáticos desde comercio
$file = __DIR__ . '/comercio' . $uri;

// Si es un archivo que existe, servirlo
if (is_file($file)) {
    return false; // Dejar que PHP sirva el archivo
}

// Si es un directorio, buscar index.php
if (is_dir($file) && file_exists($file . '/index.php')) {
    include $file . '/index.php';
    exit;
}

// Si la ruta no tiene extensión, intentar agregar .php
if (!pathinfo($uri, PATHINFO_EXTENSION)) {
    $phpFile = __DIR__ . '/comercio' . $uri . '.php';
    if (file_exists($phpFile)) {
        include $phpFile;
        exit;
    }
}

// 404 para todo lo demás
http_response_code(404);
echo '404 Not Found';
?>