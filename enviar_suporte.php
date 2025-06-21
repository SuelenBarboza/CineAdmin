<?php
session_start();

if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit();
}

require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $assunto = $_POST['assunto'] ?? '';
    $mensagem = $_POST['mensagem'] ?? '';
    
    
    if (empty($assunto) || empty($mensagem)) {
        $_SESSION['suporte_erro'] = "Por favor, preencha todos os campos.";
        header('Location: suporte_usuario.php');
        exit();
    }
    
    
    $mysqli = conexao();
    $query = "INSERT INTO tickets_suporte (usuario_id, assunto, mensagem, data_abertura) 
              VALUES (?, ?, ?, NOW())";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("iss", $usuario_id, $assunto, $mensagem);
    
    if ($stmt->execute()) {
        $_SESSION['suporte_sucesso'] = "Sua mensagem foi enviada com sucesso! Nossa equipe entrará em contato em breve.";
    } else {
        $_SESSION['suporte_erro'] = "Ocorreu um erro ao enviar sua mensagem. Por favor, tente novamente.";
    }
    
    $stmt->close();
    header('Location: suporte_usuario.php');
    exit();
}