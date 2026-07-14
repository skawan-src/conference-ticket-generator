<?php
$host = "mysql";
$user = "root";
$password = "senha";
$db = "cadastro";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8",
        $user,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );
    
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}
