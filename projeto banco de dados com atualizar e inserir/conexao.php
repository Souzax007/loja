<?php
function validarEntrada($dados){
    $dados = trim($dados);
    $dados = stripslashes($dados);
    $dados = htmlspecialchars($dados);
    return $dados;
}

$host = "localhost";
$user = "root";
$password = "linuxville";
$db = "logins";

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_error) {
    die("Conexão falhou: " . $mysqli->connect_error);
}
?>
