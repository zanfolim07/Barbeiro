<?php
// Configurações do Banco de Dados no XAMPP
$host    = 'localhost';
$banco   = 'barbearia';
$usuario = 'root';
$senha   = ''; // Padrão do XAMPP é sem senha

try {
    // Cria a conexão via PDO com charset UTF-8 (para acentos e caracteres especiais)
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8", $usuario, $senha);
    
    // Configura o PDO para lançar exceções caso ocorra algum erro de SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Exibe mensagem de erro amigável e encerra o script se a conexão falhar
    die("Erro crítico de conexão com o banco de dados: " . $e->getMessage());
}
?>