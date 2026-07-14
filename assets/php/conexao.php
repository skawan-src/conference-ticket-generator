<?php
// 1. Tenta ler as variáveis de ambiente do Render/Aiven. 
// Se não existirem (no seu PC local), ele usa os valores padrão do seu Docker local.
$host = getenv('DB_HOST') ?: 'mysql'; // 'mysql' é o nome do serviço no docker-compose
$port = getenv('DB_PORT') ?: '3306';  // Porta padrão do MySQL local
$db   = getenv('DB_NAME') ?: 'cadastro'; // Nome do seu banco local
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'senha';

try {
    // Configura a conexão PDO utilizando as variáveis dinâmicas
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    
    // Ativa o modo de erros para facilitar o debug se algo der errado
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}