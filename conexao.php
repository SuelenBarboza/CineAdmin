<?php
function conexao() {
    $host = "localhost";
    $usuario = "root";
    $senha = "";
    $banco = "filmes_admin";

    $mysqli = new mysqli($host, $usuario, $senha, $banco);

    if ($mysqli->connect_error) {
        die("Falha na conexão: " . $mysqli->connect_error);
    }

    return $mysqli;
}
?>