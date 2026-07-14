<?php
require "conexao.php";
$id = $_GET["id"];

$sql = "SELECT * FROM usuario WHERE id = :id";

$stmt = $pdo->prepare($sql);

$stmt->bindParam(":id", $id);

$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>