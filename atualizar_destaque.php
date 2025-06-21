<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['admin_logado'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $mysqli = conexao();
    
    $stmt = $mysqli->prepare("SELECT destaque FROM catalogo WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['success' => false, 'message' => 'Erro ao consultar item']);
        exit();
    }
    
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();
    
    if (!$item) {
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['success' => false, 'message' => 'Item não encontrado']);
        exit();
    }
    
    $novoDestaque = $item['destaque'] ? 0 : 1;
    
    $stmt = $mysqli->prepare("UPDATE catalogo SET destaque = ? WHERE id = ?");
    $stmt->bind_param("ii", $novoDestaque, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'destaque' => $novoDestaque]);
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar destaque']);
    }
    
    $stmt->close();
    $mysqli->close();
} else {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Requisição inválida']);
}
?>