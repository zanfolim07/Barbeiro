<?php

date_default_timezone_set('America/Sao_Paulo');

$host    = getenv('DB_HOST') ?: 'localhost';
$banco   = getenv('DB_NAME') ?: 'barbearia';
$usuario = getenv('DB_USER') ?: 'root';
$senha   = getenv('DB_PASSWORD') ?: '';

try {
    
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8", $usuario, $senha);
    
   
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log($e->getMessage());
    die('Erro crítico de conexão com o banco de dados.');
}
?>