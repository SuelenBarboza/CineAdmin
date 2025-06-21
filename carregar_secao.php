<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['admin_logado'])) {
    exit();
}

$secao = $_GET['secao'] ?? '';

switch ($secao) {
    case 'aparencia':
        include 'secoes/aparencia.php';
        break;
    case 'conta':
        include 'secoes/conta.php';
        break;
    
    default:
        echo 'Seção não encontrada';
}
?>