<?php
session_start();
require "conexao.php";

/**
 * Função para capturar o IP real do usuário (essencial para ambientes Docker)
 */
function getUserIP()
{
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    $ip = $_SERVER['REMOTE_ADDR'];

    // Se estiver rodando localmente no Docker, força um IP público para testes
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return '8.8.8.8'; // IP de teste (Google)
    }

    return $ip;
}

// Mudamos a URL para a ip-api (retorna a sigla diretamente no campo 'region')
$userIP = getUserIP();
$url = "http://ip-api.com/json/{$userIP}?fields=status,countryCode,region,city";

try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $response = curl_exec($ch);

    if ($response) {
        $details = json_decode($response);

        if ($details && $details->status === 'success') {
            $_SESSION['cidade'] = $details->city ?? "Não identificada";

            // A API já envia 'CE' para Ceará, 'SP' para São Paulo, 'CA' para Califórnia!
            $_SESSION['estado'] = strtoupper($details->region ?? "--");

            $_SESSION['pais_sigla'] = $details->countryCode ?? "--";
        } else {
            throw new Exception("IP não localizado");
        }
    } else {
        throw new Exception("Falha de conexão");
    }
} catch (Exception $e) {
    $_SESSION['cidade'] = "Não identificada";
    $_SESSION['estado'] = "--";
    $_SESSION['pais_sigla'] = "--";
}

// Configuração das datas
$_SESSION['dia'] = date('d');
$_SESSION['mes'] = date('m');
$_SESSION['ano'] = date('Y');

$meses = [
    1 => 'Jan',
    'Fev',
    'Marc',
    'Abr',
    'Mai',
    'Jun',
    'Jul',
    'Ago',
    'Setem',
    'Out',
    'Nov',
    'Dez'
];

$indiceMes = (int)$_SESSION['mes'];
$_SESSION['mesText'] = $meses[$indiceMes];

// Processamento do Formulário POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    unset($_SESSION["usuario_nome"]);
    unset($_SESSION["usuario_email"]);
    unset($_SESSION["usuario_git"]);

    $nome = isset($_POST["nome"]) ? trim($_POST["nome"]) : "";
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $github = isset($_POST["github"]) ? trim($_POST["github"]) : "";

    try {
        // Verifica se JÁ EXISTE um cadastro com o mesmo email
        $checkSql = "SELECT * FROM usuario WHERE email = :email";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindParam(":email", $email);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            $usuarioExistente = $checkStmt->fetch(PDO::FETCH_ASSOC);

            $_SESSION["usuario_nome"] = $usuarioExistente["nome"];
            $_SESSION["usuario_email"] = $email;
            $_SESSION["usuario_git"] = $github;

            $updateSql = "UPDATE usuario SET user_git = :user_git WHERE email = :email";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->bindParam(":user_git", $github);
            $updateStmt->bindParam(":email", $email);
            $updateStmt->execute();

            header("Location: /../ticket.php");
            exit;
        }

        // Se NÃO existe, faz o INSERT de um novo usuário
        $sql = "INSERT INTO usuario(nome, email, user_git) VALUES (:nome, :email, :user_git)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":user_git", $github);
        $stmt->execute();

        $_SESSION["usuario_nome"] = $nome;
        $_SESSION["usuario_email"] = $email;
        $_SESSION["usuario_git"] = $github;

        header("Location: /../ticket.php");
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            $fallbackStmt = $pdo->prepare("SELECT nome FROM usuario WHERE email = :email");
            $fallbackStmt->bindParam(":email", $email);
            $fallbackStmt->execute();
            $user = $fallbackStmt->fetch(PDO::FETCH_ASSOC);

            $_SESSION["usuario_nome"] = $user ? $user["nome"] : $nome;
            $_SESSION["usuario_email"] = $email;

            header("Location: /../ticket.php");
            exit;
        } else {
            die("Erro crítico no servidor de banco de dados.");
        }
    }
}

header("Location: /ticket.php");
exit;
