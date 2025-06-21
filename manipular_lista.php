<?php
session_start();
require_once 'conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_logado'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit();
}

$acao = $_POST['acao'] ?? '';
$conteudo_id = intval($_POST['conteudo_id'] ?? 0);
$usuario_id = $_SESSION['usuario_id'];

$mysqli = conexao();

try {
    if ($acao === 'adicionar') {
        
        $stmt = $mysqli->prepare("SELECT id FROM catalogo WHERE id = ?");
        $stmt->bind_param("i", $conteudo_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Conteúdo não encontrado']);
            exit();
        }

        
        $stmt = $mysqli->prepare("SELECT id FROM lista_usuarios WHERE usuario_id = ? AND conteudo_id = ?");
        $stmt->bind_param("ii", $usuario_id, $conteudo_id);
        $stmt->execute();
        $check = $stmt->get_result();
        if ($check->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Já está na lista']);
            exit();
        }

        
        $stmt = $mysqli->prepare("INSERT INTO lista_usuarios (usuario_id, conteudo_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $usuario_id, $conteudo_id);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } elseif ($acao === 'remover') {
        
        $stmt = $mysqli->prepare("DELETE FROM lista_usuarios WHERE usuario_id = ? AND conteudo_id = ?");
        $stmt->bind_param("ii", $usuario_id, $conteudo_id);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

