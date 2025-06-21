<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['admin_logado'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit();
}

header('Content-Type: application/json');

try {
    $jsonInput = file_get_contents('php://input');
    $dados = json_decode($jsonInput, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Dados JSON inválidos');
    }

    $admin_id = $_SESSION['admin_id'];
    $secao = $dados['secao'] ?? '';
    $dados_config = $dados['dados'] ?? [];
    
    if (empty($secao)) {
        throw new Exception('Seção não especificada');
    }

    
    $mysqli = conexao();
    $query = "SELECT configuracoes FROM configuracoes_sistema WHERE admin_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $configuracoes = [];
    if ($result->num_rows > 0) {
        $configuracoes = json_decode($result->fetch_assoc()['configuracoes'], true) ?? [];
    }
    
    
    $configuracoes[$secao] = $dados_config;
    
  
    $config_json = json_encode($configuracoes);
    
    if ($result->num_rows > 0) {
        $query = "UPDATE configuracoes_sistema SET configuracoes = ? WHERE admin_id = ?";
    } else {
        $query = "INSERT INTO configuracoes_sistema (admin_id, configuracoes) VALUES (?, ?)";
    }
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("si", $config_json, $admin_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Configurações salvas com sucesso']);
    } else {
        throw new Exception("Erro ao salvar: " . $stmt->error);
    }
    
    $stmt->close();
    $mysqli->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>