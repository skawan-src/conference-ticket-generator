<?php

require "conexao.php";

$sql = "SELECT * FROM usuario";

$stmt = $pdo->query($sql);

$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($usuarios as $user){

    echo $user["nome"];

    echo "<br>";

    echo $user["email"];

    echo "<br>";

    echo $user["user_git"];

    echo "<hr>";

}